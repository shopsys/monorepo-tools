#!/usr/bin/env bash

# Rewrite git history so that only commits that made changes in a subdirectory are kept and rewrite all filepaths as if it was root
# You can use arguments for "git rev-list" to specify what commits to rewrite (defaults to rewriting history of the checked-out branch)
#
# Usage: rewrite_history_from.sh <subdirectory> [<rev-list-args>]
#
# Example: rewrite_history_from.sh packages/alpha
# Example: rewrite_history_from.sh main-repository --branches

SUBDIRECTORY=$1
REV_LIST_PARAMS=${@:2}
echo "Rewriting history from a subdirectory '$SUBDIRECTORY'"
# All paths in the index that are not prefixed with a subdirectory are removed via "git rm --cached"
# Piping through "echo -e" is needed to resolve pathnames containing unicode characters
# If there are any files in the index all paths have the subdirectory prefix removed and the index is updated
# Previous index file is replaced by a new one (otherwise each file would be in the index twice)
# Only non-empty are filtered by the commit-filter
SUBDIRECTORY=$SUBDIRECTORY SUBDIRECTORY_SED=${SUBDIRECTORY//-/\\-} git filter-branch \
    --index-filter '
    git ls-files | grep -vE "^\"*$SUBDIRECTORY/" | xargs echo -e | xargs -r git rm -q --cached
    if [ "$(git ls-files)" != "" ]; then
        git ls-files -s | sed -r "s-(\t\"*)$SUBDIRECTORY_SED/-\1-" | GIT_INDEX_FILE=$GIT_INDEX_FILE.new git update-index --index-info && mv $GIT_INDEX_FILE.new $GIT_INDEX_FILE
    fi' \
    --commit-filter 'git_commit_non_empty_tree "$@"' \
    -- $REV_LIST_PARAMS

