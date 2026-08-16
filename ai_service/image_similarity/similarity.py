from __future__ import annotations

import math


def cosine_similarity(a: list[float], b: list[float]) -> float:
    """
    Cosine similarity between two vectors.
    Returns a value roughly between -1 and 1.
    """

    if not a or not b:
        return 0.0

    if len(a) != len(b):
        raise ValueError("Embedding dimensions do not match.")

    dot = sum(x * y for x, y in zip(a, b))

    norm_a = math.sqrt(sum(x * x for x in a))
    norm_b = math.sqrt(sum(y * y for y in b))

    if norm_a == 0 or norm_b == 0:
        return 0.0

    similarity = dot / (norm_a * norm_b)

    return float(max(-1.0, min(1.0, similarity)))


def similarity_percentage(similarity: float) -> float:
    """
    Convert cosine similarity (-1..1) to a 0-100 display scale.
    """

    score = ((similarity + 1.0) / 2.0) * 100.0

    return round(max(0.0, min(100.0, score)), 2)


def classify_similarity(similarity: float) -> tuple[str, str]:
    """
    Initial similarity bands. Tune later with real RADIUS data.
    """

    if similarity >= 0.95:
        return (
            "very_high",
            "Very high visual similarity; possible reused image.",
        )

    if similarity >= 0.85:
        return (
            "high",
            "High visual similarity; manual review recommended.",
        )

    if similarity >= 0.70:
        return (
            "medium",
            "Moderate visual similarity.",
        )

    return (
        "low",
        "No strong visual similarity detected.",
    )


def similarity_to_risk_score(similarity: float) -> float:
    """
    Map cosine similarity directly to a 0-100 fraud RISK score
    (not the same as similarity_percentage — this is asymmetric,
    since only high similarity should be treated as risky).
    """

    if similarity >= 0.95:
        return 95.0

    if similarity >= 0.85:
        return 70.0

    if similarity >= 0.70:
        return 35.0

    return 5.0