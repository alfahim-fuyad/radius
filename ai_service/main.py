from __future__ import annotations

import os
import re
from functools import lru_cache
from pathlib import Path
from typing import Any

import imagehash
import pandas as pd
import requests
from fastapi import FastAPI, File, HTTPException, UploadFile
from PIL import Image
from pydantic import BaseModel, Field
from sklearn.compose import ColumnTransformer
from sklearn.ensemble import RandomForestRegressor
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.naive_bayes import MultinomialNB
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import OneHotEncoder

try:
    # Package imports are required when uvicorn starts from the project root.
    from ai_service.model.image_encoder import encode_image
    from ai_service.image_similarity.embedding import load_image_from_bytes
    from ai_service.image_similarity.similarity import (
        classify_similarity,
        cosine_similarity,
        similarity_percentage,
        similarity_to_risk_score,
    )
except ModuleNotFoundError:
    # Keep direct execution from inside ai_service working for local scripts.
    from model.image_encoder import encode_image
    from image_similarity.embedding import load_image_from_bytes
    from image_similarity.similarity import (
        classify_similarity,
        cosine_similarity,
        similarity_percentage,
        similarity_to_risk_score,
    )


# ============================================================
# PATHS
# ============================================================

BASE = Path(__file__).resolve().parent
DATA = BASE / "data"

PRICE_CSV = DATA / "market_prices_expanded.csv"
FRAUD_CSV = DATA / "fraud_listings_synthetic.csv"


# ============================================================
# ENVIRONMENT VARIABLES
# ============================================================

SERPAPI_KEY = os.getenv("SERPAPI_KEY", "").strip()

SERPAPI_URL = "https://serpapi.com/search.json"

SEARCH_TIMEOUT = 10


# ============================================================
# RISK CONFIGURATION
# ============================================================

IMAGE_HIGH_MAX = 5
IMAGE_MEDIUM_MAX = 10

BANNED_TERMS = {
    "whatsapp me",
    "telegram me",
    "pay first",
    "send advance",
    "outside platform only",
    "bkash first",
    "urgent advance",
}

PROHIBITED_TERMS = {
    "weapon",
    "gun",
    "nid card",
    "passport for sale",
    "exam paper",
    "bank account for sale",
}


# ============================================================
# FASTAPI
# ============================================================

app = FastAPI(
    title="RADIUS Explainable Fraud Service",
    version="2.1.0",
)


# ============================================================
# REQUEST MODELS
# ============================================================

class AnalyzeRequest(BaseModel):
    title: str
    description: str
    category: str
    brand: str = ""
    condition: str
    price: float = Field(gt=0)

    seller_information: dict[str, Any] = Field(
        default_factory=dict
    )

    image_hashes: list[str] = Field(
        default_factory=list
    )

    existing_image_hashes: list[str] = Field(
        default_factory=list
    )

    existing_descriptions: list[str] = Field(
        default_factory=list
    )


class ImageCompareRequest(BaseModel):
    query_embedding: list[float]

    existing_embeddings: list[dict[str, Any]] = Field(
        default_factory=list
    )


# ============================================================
# HEALTH CHECK
# ============================================================

@app.get("/")
def root() -> dict[str, Any]:
    return {
        "service": "RADIUS Explainable Fraud Service",
        "status": "ok",
        "version": "2.1.0",
        "price_search": "SerpApi Google Search + ML fallback",
        "image_model": "ResNet50",
        "image_embedding_dimension": 2048,
    }


@app.get("/health")
def health() -> dict[str, Any]:
    return {
        "status": "ok",
        "serpapi_configured": bool(SERPAPI_KEY),
        "image_model": "ResNet50",
        "version": "2.1.0",
    }


# ============================================================
# IMAGE HASH
# pHash = perceptual image hash
# ============================================================

@app.post("/hash-image")
async def hash_image(
    image: UploadFile = File(...)
) -> dict[str, str]:

    allowed_types = {
        "image/jpeg",
        "image/png",
        "image/webp",
    }

    if (
        image.content_type
        and image.content_type not in allowed_types
    ):
        raise HTTPException(
            status_code=400,
            detail="Unsupported image type",
        )

    try:
        opened = Image.open(image.file).convert("RGB")

        digest = imagehash.phash(opened)

    except Exception as exc:
        raise HTTPException(
            status_code=400,
            detail="Invalid image",
        ) from exc

    return {
        "image_hash": str(digest)
    }


# ============================================================
# IMAGE EMBEDDING
# ML = ResNet50
# Output = 2048 dimensional visual feature vector
# ============================================================

@app.post("/image-embedding")
async def image_embedding(
    image: UploadFile = File(...),
) -> dict[str, Any]:

    allowed_types = {
        "image/jpeg",
        "image/png",
        "image/webp",
    }

    if (
        image.content_type
        and image.content_type not in allowed_types
    ):
        raise HTTPException(
            status_code=400,
            detail="Unsupported image type",
        )

    try:
        image_bytes = await image.read()

        if not image_bytes:
            raise ValueError("Empty image file.")

        opened = load_image_from_bytes(
            image_bytes
        )

        embedding = encode_image(
            opened
        )

        return {
            "success": True,
            "model": "ResNet50",
            "embedding_dimension": len(embedding),
            "embedding": embedding,
        }

    except ValueError as exc:
        raise HTTPException(
            status_code=400,
            detail=str(exc),
        ) from exc

    except Exception as exc:
        raise HTTPException(
            status_code=500,
            detail=(
                "Could not generate image embedding."
            ),
        ) from exc


# ============================================================
# IMAGE COMPARISON
# ML = cosine similarity
# ============================================================

@app.post("/compare-image")
def compare_image(
    request: ImageCompareRequest,
) -> dict[str, Any]:

    query = request.query_embedding

    if not query:
        raise HTTPException(
            status_code=400,
            detail="Query embedding is empty.",
        )

    matches: list[dict[str, Any]] = []

    for item in request.existing_embeddings:

        embedding = item.get(
            "embedding",
            []
        )

        if (
            not isinstance(embedding, list)
            or not embedding
        ):
            continue

        try:
            similarity = cosine_similarity(
                query,
                embedding
            )

        except ValueError:
            continue

        level, reason = classify_similarity(
            similarity
        )

        matches.append(
            {
                "listing_id": item.get(
                    "listing_id"
                ),
                "similarity": round(
                    similarity,
                    6
                ),
                "similarity_percentage": (
                    similarity_percentage(
                        similarity
                    )
                ),
                "risk_score": (
                    similarity_to_risk_score(
                        similarity
                    )
                ),
                "level": level,
                "reason": reason,
            }
        )

    matches.sort(
        key=lambda x: x["similarity"],
        reverse=True
    )

    best_match = (
        matches[0]
        if matches
        else None
    )

    if best_match is None:
        return {
            "success": True,
            "same_image": False,
            "best_match": None,
            "matches": [],
        }

    same_image = (
        best_match["similarity"] >= 0.95
    )

    return {
        "success": True,
        "same_image": same_image,
        "best_match": best_match,
        "matches": matches[:10],
    }


# ============================================================
# pHASH HAMMING DISTANCE
# ============================================================

def hamming(
    a: str,
    b: str,
) -> int:

    try:
        return (
            int(a, 16) ^ int(b, 16)
        ).bit_count()

    except (
        TypeError,
        ValueError
    ):
        return 999


# ============================================================
# IMAGE RISK
#
# Used by /analyze-listing
#
# IMPORTANT:
# This part uses pHash.
# ResNet50 similarity is available separately through
# /image-embedding + /compare-image.
# ============================================================

def image_risk(
    own: list[str],
    existing: list[str],
) -> tuple[float, str]:

    if not own or not existing:
        return (
            0.0,
            "No matching historical image was found.",
        )

    nearest = min(
        hamming(a, b)
        for a in own
        for b in existing
    )

    if nearest <= IMAGE_HIGH_MAX:

        return (
            95.0,
            (
                "Possible reused/stolen image signal: "
                f"perceptual hash distance is {nearest}."
            ),
        )

    if nearest <= IMAGE_MEDIUM_MAX:

        return (
            60.0,
            (
                "Uploaded image is visually similar "
                "to an existing listing "
                f"(hash distance {nearest})."
            ),
        )

    return (
        5.0,
        (
            "No close perceptual-image match; "
            f"nearest hash distance is {nearest}."
        ),
    )


# ============================================================
# PRICE MODEL
# FALLBACK ONLY
#
# Primary source:
# SerpApi Google search
#
# Fallback:
# RandomForest trained on CSV
# ============================================================

@lru_cache(maxsize=1)
def price_model() -> Pipeline:

    if not PRICE_CSV.exists():

        raise FileNotFoundError(
            f"Price dataset not found: {PRICE_CSV}"
        )

    df = pd.read_csv(
        PRICE_CSV
    )

    required_columns = {
        "category",
        "brand",
        "condition",
        "price",
    }

    missing = (
        required_columns
        - set(df.columns)
    )

    if missing:

        raise ValueError(
            "Price dataset is missing columns: "
            + ", ".join(
                sorted(missing)
            )
        )

    features = [
        "category",
        "brand",
        "condition",
    ]

    model = Pipeline(
        [
            (
                "prep",
                ColumnTransformer(
                    [
                        (
                            "cat",
                            OneHotEncoder(
                                handle_unknown="ignore"
                            ),
                            features,
                        )
                    ]
                ),
            ),

            (
                "rf",
                RandomForestRegressor(
                    n_estimators=120,
                    random_state=42,
                    min_samples_leaf=2,
                ),
            ),
        ]
    )

    model.fit(
        df[features],
        df["price"],
    )

    return model


def ml_market_price(
    category: str,
    brand: str,
    condition: str,
) -> float:

    expected = float(
        price_model().predict(
            pd.DataFrame(
                [
                    {
                        "category": category,
                        "brand": (
                            brand
                            or "generic"
                        ),
                        "condition": condition,
                    }
                ]
            )
        )[0]
    )

    return max(
        expected,
        1.0
    )


# ============================================================
# PRICE SEARCH QUERY
# ============================================================

def build_price_search_query(
    title: str,
    category: str,
    brand: str,
    condition: str,
) -> str:

    parts: list[str] = []

    if brand:
        parts.append(
            brand
        )

    if title:
        parts.append(
            title
        )

    if category:
        parts.append(
            category
        )

    condition_lower = (
        condition
        .lower()
        .strip()
    )

    if condition_lower in {
        "used",
        "excellent",
        "good",
        "fair",
        "poor",
    }:

        parts.append(
            "used"
        )

    parts.extend(
        [
            "price",
            "Bangladesh",
            "BDT",
        ]
    )

    return " ".join(
        str(x).strip()
        for x in parts
        if str(x).strip()
    )


# ============================================================
# NUMBER NORMALIZATION
# ============================================================

def normalize_number(
    value: str,
) -> float | None:

    if not value:
        return None

    text = (
        value
        .strip()
        .lower()
    )

    text = text.replace(
        ",",
        ""
    )

    text = text.replace(
        " ",
        ""
    )

    multiplier = 1.0

    match = re.search(
        r"(\d+(?:\.\d+)?)\s*"
        r"(k|thousand|lakh|lac|crore)?",
        text,
    )

    if not match:
        return None

    try:

        number = float(
            match.group(1)
        )

    except ValueError:

        return None

    suffix = match.group(2)

    if suffix == "k":
        multiplier = 1_000

    elif suffix == "thousand":
        multiplier = 1_000

    elif suffix in {
        "lakh",
        "lac",
    }:
        multiplier = 100_000

    elif suffix == "crore":
        multiplier = 10_000_000

    result = (
        number
        * multiplier
    )

    if result <= 0:
        return None

    if result > 100_000_000:
        return None

    return result


# ============================================================
# PRICE EXTRACTION
# ============================================================

def extract_prices(
    text: str,
) -> list[float]:

    if not text:
        return []

    text = str(
        text
    )

    patterns = [

        # BDT 50,000
        r"(?:bdt|৳|tk\.?|taka)\s*"
        r"(\d[\d,]*(?:\.\d+)?)",

        # 50,000 BDT
        r"(\d[\d,]*(?:\.\d+)?)\s*"
        r"(?:bdt|৳|tk\.?|taka)",

        # INR
        r"(?:rs\.?|₹)\s*"
        r"(\d[\d,]*(?:\.\d+)?)",

        # comma formatted numbers
        r"\b(\d{1,3}"
        r"(?:,\d{3})+"
        r"(?:\.\d+)?)\b",

        # large plain numbers
        r"\b(\d{5,7})\b",
    ]

    prices: list[float] = []

    for pattern in patterns:

        matches = re.findall(
            pattern,
            text,
            flags=re.IGNORECASE,
        )

        for match in matches:

            value = normalize_number(
                str(match)
            )

            if value is None:
                continue

            if (
                100
                <= value
                <= 10_000_000
            ):
                prices.append(
                    value
                )

    unique_prices: list[float] = []

    for price in prices:

        rounded = round(
            price,
            2
        )

        if rounded not in unique_prices:

            unique_prices.append(
                rounded
            )

    return unique_prices


# ============================================================
# SERPAPI SEARCH
# ============================================================

def serpapi_search(
    query: str,
) -> dict[str, Any] | None:

    if not SERPAPI_KEY:
        return None

    params = {
        "engine": "google",
        "q": query,
        "api_key": SERPAPI_KEY,
        "num": 10,
        "hl": "en",
        "gl": "bd",
        "location": "Bangladesh",
    }

    try:

        response = requests.get(
            SERPAPI_URL,
            params=params,
            timeout=SEARCH_TIMEOUT,
        )

        response.raise_for_status()

        data = response.json()

        if not isinstance(
            data,
            dict
        ):
            return None

        if data.get("error"):
            return None

        return data

    except (
        requests.RequestException,
        ValueError,
    ):

        return None


# ============================================================
# RESULT RELEVANCE
# ============================================================

def relevance_score(
    title: str,
    category: str,
    brand: str,
    result_text: str,
) -> int:

    text = result_text.lower()

    score = 0

    title_words = re.findall(
        r"[a-z0-9]+",
        title.lower(),
    )

    for word in title_words:

        if (
            len(word) >= 3
            and word in text
        ):

            score += 2

    if category:

        if category.lower() in text:
            score += 3

    if brand:

        if brand.lower() in text:
            score += 4

    if any(
        word in text
        for word in [
            "price",
            "৳",
            "bdt",
            "tk",
            "taka",
        ]
    ):

        score += 2

    if any(
        word in text
        for word in [
            "bangladesh",
            "dhaka",
            "bd",
        ]
    ):

        score += 2

    return score


# ============================================================
# LIVE MARKET PRICE
# ============================================================

def live_market_price(
    title: str,
    category: str,
    brand: str,
    condition: str,
) -> dict[str, Any]:

    query = build_price_search_query(
        title=title,
        category=category,
        brand=brand,
        condition=condition,
    )

    data = serpapi_search(
        query
    )

    if not data:

        return {
            "success": False,
            "price": None,
            "query": query,
            "source": None,
            "title": None,
            "url": None,
            "reason": (
                "Live Google price search "
                "was unavailable."
            ),
        }

    candidates: list[
        dict[str, Any]
    ] = []

    # ========================================================
    # SHOPPING RESULTS
    # ========================================================

    shopping_results = data.get(
        "shopping_results",
        [],
    )

    if isinstance(
        shopping_results,
        list
    ):

        for result in shopping_results:

            if not isinstance(
                result,
                dict
            ):
                continue

            result_title = str(
                result.get(
                    "title",
                    ""
                )
            )

            snippet = str(
                result.get(
                    "snippet",
                    ""
                )
            )

            price_text = str(
                result.get(
                    "price",
                    ""
                )
            )

            combined = (
                result_title
                + " "
                + snippet
                + " "
                + price_text
            )

            prices = extract_prices(
                combined
            )

            if not prices:
                continue

            score = relevance_score(
                title,
                category,
                brand,
                combined,
            )

            candidates.append(
                {
                    "price": prices[0],
                    "score": score + 10,
                    "title": result_title,
                    "source": str(
                        result.get(
                            "source",
                            "Google Shopping",
                        )
                    ),
                    "url": str(
                        result.get(
                            "link",
                            "",
                        )
                    ),
                }
            )

    # ========================================================
    # ORGANIC RESULTS
    # ========================================================

    organic_results = data.get(
        "organic_results",
        [],
    )

    if isinstance(
        organic_results,
        list
    ):

        for result in organic_results:

            if not isinstance(
                result,
                dict
            ):
                continue

            result_title = str(
                result.get(
                    "title",
                    ""
                )
            )

            snippet = str(
                result.get(
                    "snippet",
                    ""
                )
            )

            rich_snippet = result.get(
                "rich_snippet",
                {},
            )

            if not isinstance(
                rich_snippet,
                dict
            ):

                rich_snippet = {}

            combined = (
                result_title
                + " "
                + snippet
                + " "
                + str(
                    rich_snippet
                )
            )

            prices = extract_prices(
                combined
            )

            if not prices:
                continue

            score = relevance_score(
                title,
                category,
                brand,
                combined,
            )

            candidates.append(
                {
                    "price": prices[0],
                    "score": score,
                    "title": result_title,
                    "source": str(
                        result.get(
                            "source",
                            "Google",
                        )
                    ),
                    "url": str(
                        result.get(
                            "link",
                            "",
                        )
                    ),
                }
            )

    # ========================================================
    # NO PRICE
    # ========================================================

    if not candidates:

        return {
            "success": False,
            "price": None,
            "query": query,
            "source": None,
            "title": None,
            "url": None,
            "reason": (
                "Google search completed, "
                "but no usable market price "
                "was found."
            ),
        }

    # ========================================================
    # BEST RESULT
    # ========================================================

    candidates.sort(
        key=lambda item: item["score"],
        reverse=True,
    )

    best = candidates[0]

    return {
        "success": True,
        "price": float(
            best["price"]
        ),
        "query": query,
        "source": best["source"],
        "title": best["title"],
        "url": best["url"],
        "reason": (
            "Live market price found "
            "from Google search."
        ),
    }


# ============================================================
# PRICE SCORE
# ============================================================

def calculate_price_score(
    actual: float,
    market: float,
) -> float:

    if market <= 0:
        return 0.0

    ratio = (
        actual / market
    )

    if ratio < 0.35:
        return 95.0

    if ratio < 0.50:
        return 85.0

    if ratio < 0.65:
        return 70.0

    if ratio < 0.80:
        return 50.0

    if ratio < 0.90:
        return 30.0

    if ratio <= 1.25:
        return 8.0

    if ratio <= 1.60:
        return 12.0

    return 18.0


# ============================================================
# PRICE RISK
# ============================================================

def price_risk(
    title: str,
    category: str,
    brand: str,
    condition: str,
    actual: float,
) -> tuple[
    float,
    float,
    str,
    dict[str, Any],
]:

    live = live_market_price(
        title=title,
        category=category,
        brand=brand,
        condition=condition,
    )

    # ========================================================
    # LIVE GOOGLE PRICE
    # ========================================================

    if (
        live["success"]
        and live["price"]
    ):

        expected = float(
            live["price"]
        )

        score = calculate_price_score(
            actual=actual,
            market=expected,
        )

        difference = (
            actual - expected
        )

        percentage = (
            difference
            / expected
        ) * 100

        direction = (
            "higher"
            if difference > 0
            else "lower"
            if difference < 0
            else "the same as"
        )

        reason = (
            "Live Google market price "
            f"is about BDT {expected:,.0f}; "
            f"listing price is "
            f"BDT {actual:,.0f}. "
            f"The listing is "
            f"{abs(percentage):.1f}% "
            f"{direction} than the "
            "reference price."
        )

        details = {
            "source_type": "live_google",
            "market_price": expected,
            "listing_price": actual,
            "difference": round(
                difference,
                2,
            ),
            "difference_percent": round(
                percentage,
                2,
            ),
            "search_query": live[
                "query"
            ],
            "source": live[
                "source"
            ],
            "source_title": live[
                "title"
            ],
            "source_url": live[
                "url"
            ],
        }

        return (
            score,
            expected,
            reason,
            details,
        )

    # ========================================================
    # ML FALLBACK
    # ========================================================

    try:

        expected = ml_market_price(
            category=category,
            brand=brand,
            condition=condition,
        )

        score = calculate_price_score(
            actual=actual,
            market=expected,
        )

        difference = (
            actual - expected
        )

        difference_percent = (
            difference
            / expected
        ) * 100

        reason = (
            "Live Google price was "
            "unavailable. "
            f"Fallback estimated market "
            f"price is about "
            f"BDT {expected:,.0f}; "
            f"listing price is "
            f"BDT {actual:,.0f}."
        )

        details = {
            "source_type": "ml_fallback",
            "market_price": expected,
            "listing_price": actual,
            "difference": round(
                difference,
                2,
            ),
            "difference_percent": round(
                difference_percent,
                2,
            ),
            "search_query": live.get(
                "query"
            ),
            "source": (
                "market_prices_expanded.csv"
            ),
            "source_title": None,
            "source_url": None,
        }

        return (
            score,
            expected,
            reason,
            details,
        )

    except Exception:

        return (
            0.0,
            actual,
            (
                "Market price could not "
                "be determined from Google "
                "search or fallback data."
            ),
            {
                "source_type": "unavailable",
                "market_price": None,
                "listing_price": actual,
                "difference": None,
                "difference_percent": None,
                "search_query": live.get(
                    "query"
                ),
                "source": None,
                "source_title": None,
                "source_url": None,
            },
        )


# ============================================================
# TEXT MODEL
# TF-IDF + Multinomial Naive Bayes
# ============================================================

@lru_cache(maxsize=1)
def text_model() -> Pipeline:

    if not FRAUD_CSV.exists():

        raise FileNotFoundError(
            f"Fraud dataset not found: "
            f"{FRAUD_CSV}"
        )

    df = pd.read_csv(
        FRAUD_CSV
    )

    required_columns = {
        "title",
        "description",
        "label",
    }

    missing = (
        required_columns
        - set(df.columns)
    )

    if missing:

        raise ValueError(
            "Fraud dataset is missing "
            "columns: "
            + ", ".join(
                sorted(missing)
            )
        )

    model = Pipeline(
        [
            (
                "tfidf",
                TfidfVectorizer(
                    ngram_range=(1, 2),
                    min_df=1,
                ),
            ),

            (
                "nb",
                MultinomialNB(),
            ),
        ]
    )

    text = (
        df["title"]
        .fillna("")
        + " "
        + df["description"]
        .fillna("")
    )

    model.fit(
        text,
        df["label"],
    )

    return model


# ============================================================
# TEXT SIMILARITY
# ============================================================

def _similarity(
    a: str,
    b: str,
) -> float:

    wa = set(
        re.findall(
            r"[a-z0-9]+",
            a.lower(),
        )
    )

    wb = set(
        re.findall(
            r"[a-z0-9]+",
            b.lower(),
        )
    )

    return (
        len(wa & wb)
        / max(
            len(wa | wb),
            1
        )
    )


# ============================================================
# TEXT RISK
# ============================================================

def text_risk(
    title: str,
    description: str,
    existing: list[str],
) -> tuple[float, str]:

    text = (
        f"{title} {description}"
        .lower()
        .strip()
    )

    # --------------------------------------------------------
    # ML probability
    # --------------------------------------------------------

    try:

        model = text_model()

        classes = list(
            model.named_steps[
                "nb"
            ].classes_
        )

        probs = model.predict_proba(
            [text]
        )[0]

        if "suspicious" in classes:

            suspicious_prob = float(
                probs[
                    classes.index(
                        "suspicious"
                    )
                ]
            )

        else:

            suspicious_prob = 0.0

    except Exception:

        suspicious_prob = 0.0

    # --------------------------------------------------------
    # Text reuse
    # --------------------------------------------------------

    reused = any(
        _similarity(
            text,
            x.lower(),
        ) >= 0.92

        for x in existing

        if x
    )

    # --------------------------------------------------------
    # Final text score
    # --------------------------------------------------------

    score = min(
        100.0,
        suspicious_prob * 100
        + (
            25
            if reused
            else 0
        ),
    )

    reason = (
        "TF-IDF/Naive Bayes "
        "suspicious-text probability "
        f"is {suspicious_prob:.0%}."
    )

    if reused:

        reason += (
            " Listing text is also "
            "highly similar to an "
            "existing description."
        )

    return (
        score,
        reason
    )


# ============================================================
# SELLER RISK
# ============================================================

def seller_risk(
    s: dict[str, Any],
) -> tuple[float, str]:

    score = 0.0

    reasons: list[str] = []

    # --------------------------------------------------------
    # Account age
    # --------------------------------------------------------

    try:

        account_age = int(
            s.get(
                "account_age_days",
                0,
            )
            or 0
        )

    except (
        TypeError,
        ValueError
    ):

        account_age = 0

    if account_age < 7:

        score += 28

        reasons.append(
            "new account"
        )

    # --------------------------------------------------------
    # Reports
    # --------------------------------------------------------

    try:

        reports = int(
            s.get(
                "report_count",
                0,
            )
            or 0
        )

    except (
        TypeError,
        ValueError
    ):

        reports = 0

    score += min(
        30,
        reports * 10
    )

    # --------------------------------------------------------
    # Removed listings
    # --------------------------------------------------------

    try:

        removed = int(
            s.get(
                "removed_listings",
                0,
            )
            or 0
        )

    except (
        TypeError,
        ValueError
    ):

        removed = 0

    score += min(
        25,
        removed * 12
    )

    # --------------------------------------------------------
    # Suspicious listings
    # --------------------------------------------------------

    try:

        suspicious = int(
            s.get(
                "suspicious_listings",
                0,
            )
            or 0
        )

    except (
        TypeError,
        ValueError
    ):

        suspicious = 0

    score += min(
        20,
        suspicious * 7
    )

    # --------------------------------------------------------
    # Positive history
    # --------------------------------------------------------

    try:

        completed = int(
            s.get(
                "completed_trades",
                0,
            )
            or 0
        )

    except (
        TypeError,
        ValueError
    ):

        completed = 0

    try:

        rating = float(
            s.get(
                "rating_average",
                0,
            )
            or 0
        )

    except (
        TypeError,
        ValueError
    ):

        rating = 0.0

    if (
        completed >= 5
        and rating >= 4
    ):

        score -= 18

        reasons.append(
            "positive completed-trade history"
        )

    score = max(
        0.0,
        min(
            100.0,
            score
        )
    )

    return (
        score,
        "Seller signals: "
        + (
            ", ".join(reasons)
            if reasons
            else (
                "no strong historical "
                "risk signal"
            )
        )
        + ".",
    )


# ============================================================
# POLICY RISK
# ============================================================

def policy_risk(
    title: str,
    description: str,
    brand: str,
) -> tuple[float, str]:

    text = (
        f"{title} {description}"
        .lower()
    )

    # --------------------------------------------------------
    # Banned / prohibited phrases
    # --------------------------------------------------------

    hits = [
        x
        for x in (
            BANNED_TERMS
            | PROHIBITED_TERMS
        )
        if x in text
    ]

    # --------------------------------------------------------
    # Brand mismatch
    # --------------------------------------------------------

    mismatch = False

    known = {
        "apple": [
            "iphone",
            "ipad",
            "macbook",
        ],

        "samsung": [
            "galaxy",
            "samsung",
        ],

        "dell": [
            "xps",
            "latitude",
            "inspiron",
        ],
    }

    mentioned = [
        b
        for b, words in known.items()
        if any(
            w in text
            for w in words
        )
    ]

    if (
        brand
        and mentioned
        and brand.lower()
        not in mentioned
    ):

        mismatch = True

    score = min(
        100,
        len(hits) * 30
        + (
            35
            if mismatch
            else 0
        ),
    )

    reasons: list[str] = []

    if hits:

        reasons.append(
            "policy/off-platform phrases: "
            + ", ".join(
                hits[:3]
            )
        )

    if mismatch:

        reasons.append(
            "brand field conflicts "
            "with recognizable "
            "product wording"
        )

    return (
        float(score),
        "; ".join(
            reasons
        )
        if reasons
        else (
            "No major policy or "
            "brand mismatch signal."
        ),
    )


# ============================================================
# ANALYZE LISTING
# ============================================================

@app.post("/analyze-listing")
def analyze(
    p: AnalyzeRequest,
) -> dict[str, Any]:

    # ========================================================
    # IMAGE
    # ========================================================

    image_score, image_reason = image_risk(
        p.image_hashes,
        p.existing_image_hashes,
    )

    # ========================================================
    # PRICE
    # ========================================================

    (
        price_score,
        expected,
        price_reason,
        price_details,
    ) = price_risk(
        title=p.title,
        category=p.category,
        brand=p.brand,
        condition=p.condition,
        actual=p.price,
    )

    # ========================================================
    # SELLER
    # ========================================================

    seller_score, seller_reason = seller_risk(
        p.seller_information
    )

    # ========================================================
    # TEXT
    # ========================================================

    text_score, text_reason = text_risk(
        p.title,
        p.description,
        p.existing_descriptions,
    )

    # ========================================================
    # POLICY
    # ========================================================

    policy_score, policy_reason = policy_risk(
        p.title,
        p.description,
        p.brand,
    )

    # ========================================================
    # FINAL FRAUD SCORE
    #
    # Image  = 25%
    # Price  = 25%
    # Seller = 20%
    # Text   = 20%
    # Policy = 10%
    # ========================================================

    total = round(
        image_score * 0.25
        + price_score * 0.25
        + seller_score * 0.20
        + text_score * 0.20
        + policy_score * 0.10,
        2,
    )

    # ========================================================
    # STATUS
    # ========================================================

    if total < 30:

        status = "safe"

    elif total < 50:

        status = "low_risk"

    elif total < 70:

        status = "suspicious"

    else:

        status = "high_risk"

    # ========================================================
    # EXPLANATION
    # ========================================================

    scored_reasons = [
        (
            image_score,
            image_reason,
        ),
        (
            price_score,
            price_reason,
        ),
        (
            seller_score,
            seller_reason,
        ),
        (
            text_score,
            text_reason,
        ),
        (
            policy_score,
            policy_reason,
        ),
    ]

    reasons = [
        reason
        for score, reason
        in scored_reasons
        if score >= 30
    ]

    explanation = (
        " ".join(reasons)
        if reasons
        else (
            "No strong fraud "
            "indicators were detected."
        )
    )

    # ========================================================
    # RESPONSE
    # ========================================================

    return {

        # ----------------------------------------------------
        # Overall result
        # ----------------------------------------------------

        "fraud_score": total,

        "trust_status": status,

        # ----------------------------------------------------
        # Individual risk scores
        # ----------------------------------------------------

        "image_score": round(
            image_score,
            2,
        ),

        "price_score": round(
            price_score,
            2,
        ),

        "seller_score": round(
            seller_score,
            2,
        ),

        "text_score": round(
            text_score,
            2,
        ),

        "policy_score": round(
            policy_score,
            2,
        ),

        # ----------------------------------------------------
        # Market price
        # ----------------------------------------------------

        "estimated_market_price": (
            round(
                expected,
                2,
            )
            if expected
            else None
        ),

        "price_source": (
            price_details.get(
                "source_type"
            )
        ),

        "price_search_query": (
            price_details.get(
                "search_query"
            )
        ),

        "price_source_name": (
            price_details.get(
                "source"
            )
        ),

        "price_source_title": (
            price_details.get(
                "source_title"
            )
        ),

        "price_source_url": (
            price_details.get(
                "source_url"
            )
        ),

        "price_difference": (
            price_details.get(
                "difference"
            )
        ),

        "price_difference_percent": (
            price_details.get(
                "difference_percent"
            )
        ),

        # ----------------------------------------------------
        # Explanation
        # ----------------------------------------------------

        "explanation": explanation,

        # ----------------------------------------------------
        # Individual signals
        # ----------------------------------------------------

        "signals": {

            "image": image_reason,

            "price": price_reason,

            "seller": seller_reason,

            "text": text_reason,

            "policy": policy_reason,
        },

        # ----------------------------------------------------
        # Complete price details
        # ----------------------------------------------------

        "price_details": price_details,

        # ----------------------------------------------------
        # Model information
        # ----------------------------------------------------

        "model_name": (
            "RADIUS Explainable Ensemble"
        ),

        "model_version": "2.1",

        # ----------------------------------------------------
        # Input snapshot
        # ----------------------------------------------------

        "feature_snapshot": (
            p.model_dump()
        ),
    }