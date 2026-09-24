#!/usr/bin/env bash
# Compatibility wrapper — the launcher is cross-platform Node (start.js).
# Kept so existing docs/commands like ./start.sh keep working.
exec node "$(dirname "$0")/start.js" "$@"
