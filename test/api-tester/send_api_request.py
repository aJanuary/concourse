#!/usr/bin/env -S uv run --script
# /// script
# requires-python = ">=3.9"
# dependencies = ["requests>=2.31", "cryptography>=42"]
# ///
"""
CLI utility for sending signed requests to the members API (see site/lib/api.php).

Usage:
  send_api_request.py <endpoint> <json-data> [options]

Examples:
  send_api_request.py upsert-sessions '[{"itemId": "1", "title": "Panel", "destination": "Room A"}]'
  send_api_request.py delete-sessions '["1", "2"]' --client programme-tool
  send_api_request.py upsert-sessions @payload.json
  echo '["1"]' | send_api_request.py delete-sessions -

An API key encrypted in the secrets file (enc:...) is decrypted with the key
from CONFIG_ENCRYPTION_KEY, the file named by CONFIG_ENCRYPTION_KEY_FILE,
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
from datetime import datetime, timezone
from pathlib import Path
from urllib.parse import urljoin

import requests
from cryptography.exceptions import InvalidTag
from cryptography.hazmat.primitives.ciphers.aead import AESGCM

REPO_ROOT = Path(__file__).resolve().parents[2]
DEFAULT_SECRETS_FILE = REPO_ROOT / "secrets" / "web_secrets.ini"
DEFAULT_BASE_URL = "http://localhost:8080"


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
    raise SystemExit("The API key is encrypted, but no encryption key was found. Set CONFIG_ENCRYPTION_KEY or pass --key")


def decrypt_value(encrypted: str, key: str) -> str:
    """Decrypts a value made by scripts/encrypt-secret: base64 of the nonce,
    ciphertext and tag, AES-256-GCM."""
    try:
        key_bytes = base64.b64decode(key, validate=True)
        data = base64.b64decode(encrypted, validate=True)
    except ValueError:
        raise SystemExit("The encryption key or the encrypted API key isn't valid base64")
    if len(key_bytes) != 32:
        raise SystemExit("The encryption key must be 32 bytes, base64 encoded")
    try:
        return AESGCM(key_bytes).decrypt(data[:NONCE_LENGTH], data[NONCE_LENGTH:], None).decode("utf-8")
    except (InvalidTag, ValueError):
        raise SystemExit("Couldn't decrypt the API key: the encryption key is wrong or the value has been changed")


def load_api_key(secrets_file: Path, client_name: str) -> str:
    if not secrets_file.exists():
        raise SystemExit(f"Secrets file not found: {secrets_file}")

    # The file is PHP ini, which repeats keys such as providers[] and may contain %
    parser = configparser.ConfigParser(strict=False, interpolation=None)
    parser.read(secrets_file)

    if not parser.has_section("api_keys"):
        raise SystemExit(f"No [api_keys] section found in {secrets_file}")
    if not parser.has_option("api_keys", client_name):
        raise SystemExit(f"No API key for client '{client_name}' in {secrets_file}")

    api_key = ini_value(parser, "api_keys", client_name)
    if api_key.startswith(ENCRYPTED_PREFIX):
        api_key = decrypt_value(api_key[len(ENCRYPTED_PREFIX):], encryption_key(secrets_file, parser))
    return api_key


def read_data_arg(data_arg: str) -> bytes:
    if data_arg == "-":
        return sys.stdin.buffer.read()
    if data_arg.startswith("@"):
        return Path(data_arg[1:]).read_bytes()
    return data_arg.encode("utf-8")


def sign_request(method: str, request_uri: str, api_key: str, body: bytes):
    request_time = datetime.now(timezone.utc).strftime("%Y-%m-%dT%H:%M:%SZ")
    webhook_data = "\n".join([
        method.upper(),
        request_uri,
        request_time,
        base64.b64encode(body).decode("ascii"),
    ])
    signature = hmac.new(api_key.encode("utf-8"), webhook_data.encode("utf-8"), hashlib.sha256).hexdigest()
    return request_time, signature


def main():
    parser = argparse.ArgumentParser(description="Send a signed request to the members API.")
    parser.add_argument("endpoint", help="Endpoint name, e.g. 'upsert-sessions' (leading /api/ optional)")
    parser.add_argument("data", help="JSON body: inline string, '@file.json', or '-' for stdin")
    parser.add_argument("-c", "--client", default="programme-tool", help="Client name from [api_keys] (default: programme-tool)")
    parser.add_argument("-k", "--key", help="API key to sign with (overrides --secrets-file lookup)")
    parser.add_argument("--secrets-file", type=Path, default=DEFAULT_SECRETS_FILE, help=f"Path to secrets ini (default: {DEFAULT_SECRETS_FILE})")
    parser.add_argument("--base-url", default=DEFAULT_BASE_URL, help=f"API base URL (default: {DEFAULT_BASE_URL})")
    parser.add_argument("-X", "--method", default="POST", help="HTTP method (default: POST)")
    parser.add_argument("-v", "--verbose", action="store_true", help="Print request details before sending")
    args = parser.parse_args()

    endpoint = args.endpoint if args.endpoint.startswith("/") else f"/api/{args.endpoint}"
    if not endpoint.startswith("/api/"):
        endpoint = "/api" + endpoint

    raw_body = read_data_arg(args.data)
    try:
        parsed = json.loads(raw_body)
    except json.JSONDecodeError as e:
        raise SystemExit(f"Data is not valid JSON: {e}")
    body = json.dumps(parsed).encode("utf-8")

    api_key = args.key or load_api_key(args.secrets_file, args.client)

    request_time, signature = sign_request(args.method, endpoint, api_key, body)

    headers = {
        "Content-Type": "application/json",
        "Authorization": f"members:1 {args.client} {signature}",
        "X-Members-RequestTime": request_time,
    }

    url = urljoin(args.base_url, endpoint)

    if args.verbose:
        print(f"--> {args.method.upper()} {url}", file=sys.stderr)
        for k, v in headers.items():
            print(f"    {k}: {v}", file=sys.stderr)
        print(f"    body: {body.decode('utf-8')}", file=sys.stderr)
        print(file=sys.stderr)

    response = requests.request(args.method.upper(), url, headers=headers, data=body)

    print(f"HTTP {response.status_code}", file=sys.stderr)
    try:
        print(json.dumps(response.json(), indent=2))
    except ValueError:
        print(response.text)

    if not response.ok:
        sys.exit(1)


if __name__ == "__main__":
    main()
