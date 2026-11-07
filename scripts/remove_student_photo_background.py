import sys
from pathlib import Path

import cv2
import numpy as np


def remove_background(source_path: Path, destination_path: Path) -> None:
    image_bytes = np.fromfile(source_path, dtype=np.uint8)
    image = cv2.imdecode(image_bytes, cv2.IMREAD_COLOR)
    if image is None:
        raise ValueError("The uploaded file could not be decoded as an image.")

    height, width = image.shape[:2]
    if width < 20 or height < 20:
        raise ValueError("The uploaded image is too small for background removal.")

    scale = min(1.0, 1200 / max(width, height))
    if scale < 1:
        image = cv2.resize(
            image,
            (round(width * scale), round(height * scale)),
            interpolation=cv2.INTER_AREA,
        )

    height, width = image.shape[:2]
    margin_x = max(1, round(width * 0.035))
    margin_y = max(1, round(height * 0.025))
    rectangle = (
        margin_x,
        margin_y,
        width - margin_x * 2,
        height - margin_y * 2,
    )
    mask = np.zeros((height, width), dtype=np.uint8)
    background_model = np.zeros((1, 65), dtype=np.float64)
    foreground_model = np.zeros((1, 65), dtype=np.float64)

    cv2.grabCut(
        image,
        mask,
        rectangle,
        background_model,
        foreground_model,
        6,
        cv2.GC_INIT_WITH_RECT,
    )

    foreground = np.where(
        (mask == cv2.GC_FGD) | (mask == cv2.GC_PR_FGD),
        255,
        0,
    ).astype(np.uint8)
    foreground = cv2.morphologyEx(
        foreground,
        cv2.MORPH_CLOSE,
        np.ones((3, 3), dtype=np.uint8),
    )
    alpha = cv2.GaussianBlur(foreground, (0, 0), sigmaX=1.2)
    result = cv2.cvtColor(image, cv2.COLOR_BGR2BGRA)
    result[:, :, 3] = alpha

    encoded, output = cv2.imencode(".png", result)
    if not encoded:
        raise RuntimeError("Could not encode the processed photo.")
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
