#!/usr/bin/env bash

# Wipe original git history after rewrite
#
# Usage: original_refs_wipe.sh

echo 'Wiping the original history back-up'
# Original refs after history rewrite are stored in refs/original/
for ORIGINAL_REF in $(git for-each-ref --format="%(refname)" refs/original/); do
    git update-ref -d $ORIGINAL_REF
done
git reset --hard

