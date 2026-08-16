from __future__ import annotations

from io import BytesIO

from PIL import Image


def load_image_from_bytes(data: bytes) -> Image.Image:
    """
    Safely load an uploaded image from raw bytes.
    """

    if not data:
        raise ValueError("Empty image data.")

    try:
        image = Image.open(BytesIO(data))
        image = image.convert("RGB")
        return image

    except Exception as exc:
        raise ValueError("Invalid image file.") from exc