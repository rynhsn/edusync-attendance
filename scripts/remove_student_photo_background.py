import sys
from pathlib import Path

import cv2
import numpy as np

# Use AI background removal (rembg/U2-Net) as primary, with smart OpenCV GrabCut fallback
try:
    from PIL import Image
    from rembg import new_session, remove

    HAS_REMBG = True
except Exception:
    HAS_REMBG = False


def _read_image(source_path: Path) -> np.ndarray:
    image_bytes = np.fromfile(source_path, dtype=np.uint8)
    image = cv2.imdecode(image_bytes, cv2.IMREAD_COLOR)
    if image is None:
        raise ValueError("The uploaded file could not be decoded as an image.")
    return image


def _resize_if_needed(image: np.ndarray, max_dim: int = 1400) -> np.ndarray:
    height, width = image.shape[:2]
    scale = min(1.0, max_dim / max(width, height))
    if scale < 1.0:
        return cv2.resize(
            image,
            (round(width * scale), round(height * scale)),
            interpolation=cv2.INTER_AREA,
        )
    return image


def remove_background_ai(image: np.ndarray) -> np.ndarray:
    rgb = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
    pil_image = Image.fromarray(rgb)

    session = new_session("u2net")
    output_pil = remove(pil_image, session=session)
    rgba = np.array(output_pil)

    if rgba.shape[2] != 4:
        raise ValueError("AI segmentation did not produce an RGBA image.")

    alpha = rgba[:, :, 3]
    # Sanity check: must have sufficient foreground
    if np.count_nonzero(alpha > 128) < (alpha.size * 0.02):
        raise ValueError("AI segmentation returned an empty or nearly blank foreground.")

    # Convert RGBA to BGRA for OpenCV
    bgra = cv2.cvtColor(rgba, cv2.COLOR_RGBA2BGRA)
    return bgra


def remove_background_opencv(image: np.ndarray) -> np.ndarray:
    height, width = image.shape[:2]
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)

    # Face detection to protect head, tie, and torso
    face_cascade_path = cv2.data.haarcascades + "haarcascade_frontalface_default.xml"
    face_cascade = cv2.CascadeClassifier(face_cascade_path)
    faces = face_cascade.detectMultiScale(
        gray,
        scaleFactor=1.1,
        minNeighbors=4,
        minSize=(round(width * 0.12), round(height * 0.12)),
    )

    mask = np.full((height, width), cv2.GC_PR_BGD, dtype=np.uint8)

    # Mark upper margins and top corners as definite background
    top_h = max(2, round(height * 0.06))
    corner_w = max(2, round(width * 0.18))
    mask[:top_h, :] = cv2.GC_BGD
    mask[: round(height * 0.35), :corner_w] = cv2.GC_BGD
    mask[: round(height * 0.35), width - corner_w :] = cv2.GC_BGD

    if len(faces) > 0:
        # Pick the largest / most prominent face
        fx, fy, fw, fh = max(faces, key=lambda f: f[2] * f[3])

        # Head / face as definite foreground
        head_y1 = max(0, fy - round(fh * 0.45))
        head_y2 = min(height, fy + fh)
        head_x1 = max(0, fx - round(fw * 0.15))
        head_x2 = min(width, fx + fw + round(fw * 0.15))
        mask[head_y1:head_y2, head_x1:head_x2] = cv2.GC_FGD

        # Torso / clothes / tie protection below chin down to the bottom
        # Crucial: in student portrait photos, the body/shirt always reaches the bottom
        torso_y1 = head_y2
        torso_y2 = height
        torso_w = round(fw * 2.8)
        cx = fx + fw // 2
        torso_x1 = max(0, cx - torso_w // 2)
        torso_x2 = min(width, cx + torso_w // 2)
        mask[torso_y1:torso_y2, torso_x1:torso_x2] = cv2.GC_FGD

        # Probable foreground for shoulder spread
        shoulder_w = round(fw * 3.6)
        s_x1 = max(0, cx - shoulder_w // 2)
        s_x2 = min(width, cx + shoulder_w // 2)
        mask[torso_y1:torso_y2, s_x1:s_x2] = np.where(
            mask[torso_y1:torso_y2, s_x1:s_x2] == cv2.GC_PR_BGD,
            cv2.GC_PR_FGD,
            mask[torso_y1:torso_y2, s_x1:s_x2],
        )
    else:
        # Centered body fallback without cutting bottom
        center_x1 = round(width * 0.15)
        center_x2 = round(width * 0.85)
        center_y1 = round(height * 0.10)
        mask[center_y1:height, center_x1:center_x2] = cv2.GC_PR_FGD

    bgd_model = np.zeros((1, 65), dtype=np.float64)
    fgd_model = np.zeros((1, 65), dtype=np.float64)

    cv2.grabCut(image, mask, None, bgd_model, fgd_model, 6, cv2.GC_INIT_WITH_MASK)

    foreground_mask = np.where(
        (mask == cv2.GC_FGD) | (mask == cv2.GC_PR_FGD),
        255,
        0,
    ).astype(np.uint8)

    # Fill internal holes inside the student silhouette so tie, white uniform, badges have no holes
    contours, _ = cv2.findContours(
        foreground_mask, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE
    )
    if contours:
        filled_mask = np.zeros_like(foreground_mask)
        cv2.drawContours(filled_mask, contours, -1, 255, thickness=cv2.FILLED)
        foreground_mask = filled_mask

    foreground_mask = cv2.morphologyEx(
        foreground_mask, cv2.MORPH_CLOSE, np.ones((5, 5), dtype=np.uint8)
    )
    alpha = cv2.GaussianBlur(foreground_mask, (0, 0), sigmaX=1.2)

    bgra = cv2.cvtColor(image, cv2.COLOR_BGR2BGRA)
    bgra[:, :, 3] = alpha
    return bgra


def remove_background(source_path: Path, destination_path: Path) -> None:
    image = _read_image(source_path)
    height, width = image.shape[:2]
    if width < 20 or height < 20:
        raise ValueError("The uploaded image is too small for background removal.")

    image = _resize_if_needed(image, max_dim=1400)

    result = None
    if HAS_REMBG:
        try:
            result = remove_background_ai(image)
        except Exception:
            # Fall back to enhanced OpenCV method
            result = None

    if result is None:
        result = remove_background_opencv(image)

    encoded, output = cv2.imencode(".png", result)
    if not encoded:
        raise RuntimeError("Could not encode the processed photo.")

    destination_path.parent.mkdir(parents=True, exist_ok=True)
    output.tofile(destination_path)


def main() -> int:
    if len(sys.argv) != 3:
        print("Usage: remove_student_photo_background.py INPUT OUTPUT", file=sys.stderr)
        return 2

    try:
        remove_background(Path(sys.argv[1]), Path(sys.argv[2]))
    except (OSError, ValueError, cv2.error) as error:
        print(str(error), file=sys.stderr)
        return 1

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
