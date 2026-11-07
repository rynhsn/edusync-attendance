# Student photo background processing

New student photos are processed locally with OpenCV GrabCut and saved as transparent PNG cutouts. The ID card preview and print layout provide the solid red background behind each cutout; uploaded photos are not sent to an external service.

Install the Python dependency in the environment used by the PHP web server:

```powershell
python -m pip install -r requirements.txt
```

If Python is not available on the web server's `PATH`, set `STUDENT_PHOTO_PYTHON` to the full path of its Python executable. For example, add this to the server environment:

```text
STUDENT_PHOTO_PYTHON=C:\Path\To\Python\python.exe
```

The automatic segmentation works best when the student's full body/head is inside the photo with some background visible around the edges. Busy backgrounds, hair, transparent objects, or subjects touching the image borders can require a better-framed source photo.
