#!/usr/bin/env bash
# Compatibility wrapper — the launcher is cross-platform Node (stop.js).
exec node "$(dirname "$0")/stop.js" "$@"
