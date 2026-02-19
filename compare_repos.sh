#!/bin/bash

echo "🔍 Comparing mm-fy-web vs aws-mm-fy-web"
echo "========================================"
echo ""

# Fetch latest from both repos
echo "Fetching latest changes..."
git fetch origin --quiet
git fetch aws-repo --quiet
echo "✅ Fetch complete"
echo ""

# Compare commits
echo "=== Commit Comparison ==="
echo ""
echo "📊 Commits in mm-fy-web (origin/main):"
git log --oneline origin/main | head -5

echo ""
echo "📊 Commits in aws-mm-fy-web (aws-repo/main):"
git log --oneline aws-repo/main | head -5

echo ""
echo "=== Different Commits ==="
echo ""
echo "✨ New commits in mm-fy-web (not in aws-mm-fy-web):"
git log aws-repo/main..origin/main --oneline --no-merges

echo ""
echo "✨ New commits in aws-mm-fy-web (not in mm-fy-web):"
git log origin/main..aws-repo/main --oneline --no-merges

echo ""
echo "=== File Differences ==="
echo ""
echo "📁 Files that differ between repositories:"
git diff --name-status origin/main aws-repo/main | head -20

echo ""
echo "=== Summary ==="
commits_mm=$(git rev-list --count aws-repo/main..origin/main 2>/dev/null || echo "0")
commits_aws=$(git rev-list --count origin/main..aws-repo/main 2>/dev/null || echo "0")
files_diff=$(git diff --name-only origin/main aws-repo/main | wc -l)

echo "Commits unique to mm-fy-web: $commits_mm"
echo "Commits unique to aws-mm-fy-web: $commits_aws"
echo "Files with differences: $files_diff"

