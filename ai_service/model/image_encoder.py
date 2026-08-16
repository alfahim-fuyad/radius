from __future__ import annotations

from functools import lru_cache

import torch
import torch.nn as nn
from torchvision.models import ResNet50_Weights, resnet50


@lru_cache(maxsize=1)
def get_image_encoder() -> tuple[nn.Module, object]:
    """
    Load pretrained ResNet50 once and reuse it across requests.

    The final classification layer is replaced with an identity
    layer so the model outputs a raw visual feature embedding
    instead of ImageNet class probabilities.
    """

    weights = ResNet50_Weights.DEFAULT

    model = resnet50(weights=weights)

    model.fc = nn.Identity()

    model.eval()

    model.to(torch.device("cpu"))

    preprocess = weights.transforms()

    return model, preprocess


def encode_image(image) -> list[float]:
    """
    Convert a PIL image into a normalized 2048-dim ResNet50 embedding.
    """

    model, preprocess = get_image_encoder()

    device = torch.device("cpu")

    tensor = preprocess(image).unsqueeze(0).to(device)

    with torch.inference_mode():
        embedding = model(tensor)

    embedding = embedding.squeeze(0)

    embedding = torch.nn.functional.normalize(
        embedding,
        p=2,
        dim=0,
    )

    return embedding.cpu().tolist()