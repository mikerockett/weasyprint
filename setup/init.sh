#!/bin/sh
echo " - Copying pre-commit stub to git hooks…"
cp setup/pre-commit.stub.sh .git/hooks/pre-commit
echo " - Making executable…"
chmod +x .git/hooks/pre-commit
echo " - Done."
