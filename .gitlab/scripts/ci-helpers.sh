#!/bin/bash

# Fetch origin/main safely (no tags, no full history)
fetch_main() {
    git fetch --no-tags --unshallow origin main >/dev/null 2>&1
}

# Get list of changed files matching a glob (e.g. '*.php' or 'src/**/*.vue')
get_changed_files() {
  local base_branch="${BASE_BRANCH:-origin/main}"
  shift 0

  fetch_main

  git diff --name-only "$base_branch"...HEAD -- "$@" | grep -v '^$' || true
}
