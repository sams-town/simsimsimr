#!/usr/bin/env bash

set -euo pipefail

BASE_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$BASE_DIR"

# Activate venv if present
if [[ -f "$BASE_DIR/venv/bin/activate" ]]; then
  source "$BASE_DIR/venv/bin/activate"
fi

# Use PYTHON env if set, otherwise default to python3
PYTHON_BIN="${PYTHON:-python3}"

echo "Running Khanza migration scripts..."

"$PYTHON_BIN" script/run_journal.py
"$PYTHON_BIN" script/run_faktur.py
"$PYTHON_BIN" script/run_purchase_order.py
"$PYTHON_BIN" script/run_medical_fee.py
"$PYTHON_BIN" script/run_ar.py

echo "All migration scripts finished."

