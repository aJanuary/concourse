#!/usr/bin/env -S uv run --script
# /// script
# requires-python = ">=3.9"
# dependencies = ["requests>=2.31"]
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
"""

import argparse
import base64
import configparser
import hashlib
import hmac
import json
import sys
import uuid
from datetime import datetime, timezone
from pathlib import Path

import requests

REPO_ROOT = Path(__file__).resolve().parents[2]
DEFAULT_SECRETS_FILE = REPO_ROOT / "secrets" / "web_secrets.ini"
DEFAULT_URL = "http://localhost:8080/site-selection/square-webhook"


def load_signature_key(secrets_file: Path) -> str:
    if not secrets_file.exists():
        raise SystemExit(f"Secrets file not found: {secrets_file}")

    # The file is PHP ini, which repeats keys such as providers[] and may contain %
    parser = configparser.ConfigParser(strict=False, interpolation=None)
    parser.read(secrets_file)
    if not parser.has_option("square", "webhook_signature_key"):
        raise SystemExit(f"No [square] webhook_signature_key in {secrets_file}; pass --signature-key")

    key = parser.get("square", "webhook_signature_key").strip().strip('"').strip("'")
    if key.startswith("enc:"):
        raise SystemExit("The signature key in the secrets file is encrypted; pass --signature-key")
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
