# RADIUS

RADIUS is a PHP/MySQL hyperlocal secondhand marketplace with a separate
FastAPI service for explainable fraud-risk analysis. Listing images are stored
under `uploads/listings/`; the AI service generates a perceptual hash and a
2048-value ResNet50 embedding for each uploaded image.

## Running on Replit

The application is started with:

```bash
PORT=5000 ./run.sh
```

`run.sh` starts the FastAPI service on port `8001`, waits for its health check,
and then starts the PHP server. The PHP code calls the AI service using the
relative local URL configured by `AI_SERVICE_URL`.

The app requires a MySQL database. Set `DB_HOST`, `DB_PORT`, `DB_NAME`,
`DB_USER`, `DB_PASSWORD`, and any required TLS settings in the environment
before using database-backed pages. `SERPAPI_KEY` is optional and only affects
the price-search portion of the AI service.

## Image analysis

The upload flow calls `/hash-image` and `/image-embedding` before inserting the
row in `listing_images`. If either service call fails, the listing remains
usable but the corresponding field is left empty and the failure is written to
the PHP error log. The AI service must be running for image fraud detection to
work.