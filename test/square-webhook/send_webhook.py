#!/usr/bin/env -S uv run --script
# /// script
# requires-python = ">=3.9"
# dependencies = ["requests>=2.31", "cryptography>=42"]
# ///
"""
Sends the portal a signed Square payment.updated webhook, as Square would
when a site selection payment completes (see
site/web/site-selection/square-webhook.php). The portal then looks the order
up in Square, so use an order from the Square sandbox whose reference_id is a
member's badge number, such as one made by paying through the portal.

Usage:
  send_webhook.py <order id> [options]

Examples:
  send_webhook.py 8kPq3... --signature-key abc123
  send_webhook.py 8kPq3... --status APPROVED
  send_webhook.py 8kPq3... --url https://portal.example.com/site-selection/square-webhook

A signature key encrypted in the secrets file (enc:...) is decrypted with the
key from CONFIG_ENCRYPTION_KEY, the file named by CONFIG_ENCRYPTION_KEY_FILE,
secrets/config_encryption_key.txt next to the secrets file, or [encryption]
config_encryption_key, in that order. See "Encrypted secrets" in
docs/configuration.md.
"""

import argparse
import base64
import configparser
import hashlib
import hmac
import json
import os
import sys
import uuid
from datetime import datetime, timezone
from pathlib import Path

import requests
from cryptography.exceptions import InvalidTag
from cryptography.hazmat.primitives.ciphers.aead import AESGCM

REPO_ROOT = Path(__file__).resolve().parents[2]
DEFAULT_SECRETS_FILE = REPO_ROOT / "secrets" / "web_secrets.ini"
DEFAULT_URL = "http://localhost:8080/site-selection/square-webhook"


ENCRYPTED_PREFIX = "enc:"
NONCE_LENGTH = 12


def ini_value(parser: configparser.ConfigParser, section: str, key: str) -> str:
    # Encrypted values are quoted in the file, as base64 can end in =
    return parser.get(section, key).strip().strip('"')


def encryption_key(secrets_file: Path, parser: configparser.ConfigParser) -> str:
    """The key for encrypted secrets, looked for as the portal does."""
    key = os.environ.get("CONFIG_ENCRYPTION_KEY", "").strip()
    if key:
        return key
    key_files = [Path(p) for p in [os.environ.get("CONFIG_ENCRYPTION_KEY_FILE")] if p]
    key_files.append(secrets_file.parent / "config_encryption_key.txt")
    for key_file in key_files:
        if key_file.exists():
            key = key_file.read_text().strip()
            if key:
                return key
    if parser.has_option("encryption", "config_encryption_key"):
        key = ini_value(parser, "encryption", "config_encryption_key")
        if key:
            return key
    raise SystemExit("The signature key is encrypted, but no encryption key was found. Set CONFIG_ENCRYPTION_KEY or pass --signature-key")


def decrypt_value(encrypted: str, key: str) -> str:
    """Decrypts a value made by scripts/encrypt-secret: base64 of the nonce,
    ciphertext and tag, AES-256-GCM."""
    try:
        key_bytes = base64.b64decode(key, validate=True)
        data = base64.b64decode(encrypted, validate=True)
    except ValueError:
        raise SystemExit("The encryption key or the encrypted signature key isn't valid base64")
    if len(key_bytes) != 32:
        raise SystemExit("The encryption key must be 32 bytes, base64 encoded")
    try:
        return AESGCM(key_bytes).decrypt(data[:NONCE_LENGTH], data[NONCE_LENGTH:], None).decode("utf-8")
    except (InvalidTag, ValueError):
        raise SystemExit("Couldn't decrypt the signature key: the encryption key is wrong or the value has been changed")


def load_signature_key(secrets_file: Path) -> str:
    if not secrets_file.exists():
        raise SystemExit(f"Secrets file not found: {secrets_file}")

    # The file is PHP ini, which repeats keys such as providers[] and may contain %
    parser = configparser.ConfigParser(strict=False, interpolation=None)
    parser.read(secrets_file)
    if not parser.has_option("square", "webhook_signature_key"):
        raise SystemExit(f"No [square] webhook_signature_key in {secrets_file}; pass --signature-key")

    key = ini_value(parser, "square", "webhook_signature_key")
    if key.startswith(ENCRYPTED_PREFIX):
        key = decrypt_value(key[len(ENCRYPTED_PREFIX):], encryption_key(secrets_file, parser))
    return key


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument("order_id", help="The Square order the payment is for")
    parser.add_argument("--status", default="COMPLETED", help="The payment's status (default COMPLETED)")
    parser.add_argument("--payment-id", default=None, help="The payment's id (default random)")
    parser.add_argument("--url", default=DEFAULT_URL,
                        help=f"The notification URL, which is also signed (default {DEFAULT_URL})")
    parser.add_argument("--signature-key", default=None,
                        help="The webhook signature key (default [square] webhook_signature_key from the secrets file)")
    parser.add_argument("--secrets-file", type=Path, default=DEFAULT_SECRETS_FILE)
    args = parser.parse_args()

    key = args.signature_key or load_signature_key(args.secrets_file)
    payment_id = args.payment_id or uuid.uuid4().hex
    now = datetime.now(timezone.utc).strftime("%Y-%m-%dT%H:%M:%S.000Z")
    body = json.dumps({
        "merchant_id": "TEST",
        "type": "payment.updated",
        "event_id": str(uuid.uuid4()),
        "created_at": now,
        "data": {
            "type": "payment",
            "id": payment_id,
            "object": {
                "payment": {
                    "id": payment_id,
                    "order_id": args.order_id,
                    "status": args.status,
                    "updated_at": now,
                },
            },
        },
    })

    signature = base64.b64encode(hmac.new(key.encode(), (args.url + body).encode(), hashlib.sha256).digest()).decode()
    response = requests.post(args.url, data=body, headers={
        "Content-Type": "application/json",
        "X-Square-HmacSha256-Signature": signature,
    }, timeout=30)
    print(response.status_code, response.text)
    return 0 if response.ok else 1


if __name__ == "__main__":
    sys.exit(main())
