#!/bin/bash
# ─────────────────────────────────────────────────────────────
#  git-sync.sh — one-command backup of the Wellness website
#  Usage:  ./git-sync.sh "optional commit message"
#  If no message is given, one is generated with the timestamp.
# ─────────────────────────────────────────────────────────────
set -e
cd "$(dirname "$0")"

if [ -n "$1" ]; then
    MSG="$1"
else
    MSG="Site update $(date '+%Y-%m-%d %H:%M')"
fi

git add -A

if git diff --cached --quiet; then
    echo "✔ Nothing to commit — working tree is clean."
    exit 0
fi

git commit -m "$MSG"
git push origin main
echo "✔ Pushed to GitHub (origin/main)."
