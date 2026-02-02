#!/usr/bin/env bash
set -euo pipefail

script_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
project_root="$(cd "$script_dir/.." && pwd)"

example_file="$project_root/docker/.env.example"
target_file="$project_root/docker/.env"

if [[ ! -f "$example_file" ]]; then
  echo "Missing example file: $example_file" >&2
  exit 1
fi

if [[ -f "$target_file" ]]; then
  echo "Target file already exists: $target_file" >&2
  exit 1
fi

cp "$example_file" "$target_file"

echo "Created: $target_file"
