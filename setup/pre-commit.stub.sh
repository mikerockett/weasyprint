#!/bin/sh
files=$(git diff --cached --name-only --diff-filter=ACMR -- '*.php')
echo "Files to format: $files"
vendor/bin/stout $files
for path in $files; do
  git add $path
done
