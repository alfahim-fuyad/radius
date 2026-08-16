from __future__ import annotations

import json
import os
import sys
from pathlib import Path

import pymysql

# Allow running this script directly: python training/generate_image_embeddings.py
sys.path.append(str(Path(__file__).resolve().parent.parent))

from model.image_encoder import encode_image  # noqa: E402
from image_similarity.embedding import load_image_from_bytes  # noqa: E402


# ============================================================
# CONFIG
# ============================================================

DB_HOST = os.getenv("DB_HOST", "127.0.0.1")
DB_PORT = int(os.getenv("DB_PORT", "3306"))
DB_NAME = os.getenv("DB_NAME", "radius")
DB_USER = os.getenv("DB_USER", "root")
DB_PASSWORD = os.getenv("DB_PASSWORD", "")

# Absolute path to RADIUS project root (parent of ai_service/)
PROJECT_ROOT = Path(__file__).resolve().parent.parent.parent

UPLOADS_DIR = PROJECT_ROOT / "uploads" / "listings"

BATCH_LOG_EVERY = 20


def get_connection() -> pymysql.connections.Connection:

    return pymysql.connect(
        host=DB_HOST,
        port=DB_PORT,
        user=DB_USER,
        password=DB_PASSWORD,
        database=DB_NAME,
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
    )


def resolve_image_path(image_path: str) -> Path:
    """
    image_path is stored like '/uploads/listings/xxxx.jpg'.
    Convert it to an absolute filesystem path.
    """

    relative = image_path.lstrip("/")

    return PROJECT_ROOT / relative


def main() -> None:

    conn = get_connection()

    processed = 0
    skipped = 0
    failed = 0

    try:
        with conn.cursor() as cursor:

            cursor.execute(
                """
                SELECT id, listing_id, image_path
                FROM listing_images
                WHERE image_embedding IS NULL
                """
            )

            rows = cursor.fetchall()

        print(f"Found {len(rows)} images without embeddings.")

        for row in rows:

            image_id = row["id"]
            image_path = row["image_path"]

            abs_path = resolve_image_path(image_path)

            if not abs_path.is_file():
                print(f"[skip] file not found: {abs_path}")
                skipped += 1
                continue

            try:

                with open(abs_path, "rb") as fh:
                    data = fh.read()

                image = load_image_from_bytes(data)

                embedding = encode_image(image)

                embedding_json = json.dumps(embedding)

                with conn.cursor() as cursor:

                    cursor.execute(
                        """
                        UPDATE listing_images
                        SET image_embedding = %s
                        WHERE id = %s
                        """,
                        (embedding_json, image_id),
                    )

                conn.commit()

                processed += 1

                if processed % BATCH_LOG_EVERY == 0:
                    print(f"Processed {processed} images...")

            except Exception as exc:

                print(f"[fail] image_id={image_id}: {exc}")
                failed += 1
                conn.rollback()

        print("Done.")
        print(f"Processed: {processed}")
        print(f"Skipped:   {skipped}")
        print(f"Failed:    {failed}")

    finally:
        conn.close()


if __name__ == "__main__":
    main()