#!/usr/bin/env bash

# Delete all local branches and create all non-remote-tracking branches of a specified remote
#
# Usage: load_branches_from_remote <remote-name>
#
# Example: load_branches_from_remote origin
load_branches_from_remote() {
    REMOTE=$1
    echo "Loading all branches from the remote '$REMOTE' (all local branches are deleted)"
    # Checking out orphan commit so it 's possible to delete current branch
    git checkout --orphan void
    # Delete all local branches
    for BRANCH in `git branch`; do
        git branch -D $BRANCH
    done
    # Create non-remote-tracking branches from selected remote
    for REMOTE_BRANCH in $(git branch -r|grep $REMOTE/); do
        BRANCH=${REMOTE_BRANCH/$REMOTE\//}
        git branch -q $BRANCH $REMOTE_BRANCH
        git branch --unset-upstream $BRANCH
    done
    git reset --h
    git checkout master
}

# Restore original git history after rewrite
#
# Usage: original_refs_restore
original_refs_restore() {
    echo 'Restoring the original history back-up'
    # Original refs after history rewrite are stored in refs/original/
    for ORIGINAL_REF in $(git for-each-ref --format="%(refname)" refs/original/); do
        git update-ref "${ORIGINAL_REF#refs/original/}" $ORIGINAL_REF
        git update-ref -d $ORIGINAL_REF
    done
    git reset --hard
}

# Wipe original git history after rewrite
#
# Usage: original_refs_wipe
original_refs_wipe() {
    echo 'Wiping the original history back-up'
    # Original refs after history rewrite are stored in refs/original/
    for ORIGINAL_REF in $(git for-each-ref --format="%(refname)" refs/original/); do
        git update-ref -d $ORIGINAL_REF
    done
    git reset --hard
}

# Rewrite git history so that all filepaths are in a specific subdirectory
# You can use arguments for "git rev-list" to specify what commits to rewrite (defaults to rewriting history of the checked-out branch)
#
# Usage: rewrite_history_into <subdirectory> [<rev-list-args>]
#
# Example: rewrite_history_into packages/alpha
# Example: rewrite_history_into main-repository --branches
rewrite_history_into() {
    SUBDIRECTORY=$1
    REV_LIST_PARAMS=${@:2}
    echo "Rewriting history into a subdirectory '$SUBDIRECTORY'"
    # All paths in the index are prefixed with a subdirectory and the index is updated
    # Previous index file is replaced by a new one (otherwise each file would be in the index twice)
    SUBDIRECTORY_SED=${SUBDIRECTORY//-/\\-} git filter-branch \
        --index-filter '
        git ls-files -s | sed "s-\t\"*-&$SUBDIRECTORY_SED/-" | GIT_INDEX_FILE=$GIT_INDEX_FILE.new git update-index --index-info && mv $GIT_INDEX_FILE.new $GIT_INDEX_FILE' \
        -- $REV_LIST_PARAMS
}


# Rewrite git history so that only commits that made changes in a subdirectory are kept and rewrite all filepaths as if it was root
# You can use arguments for "git rev-list" to specify what commits to rewrite (defaults to rewriting history of the checked-out branch)
#
# Usage: rewrite_history_from <subdirectory> [<rev-list-args>]
#
# Example: rewrite_history_from packages/alpha
# Example: rewrite_history_from main-repository --branches
rewrite_history_from() {
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
}

# Build monorepo from specified remotes
# You must first add the remotes by "git remote add <remote-name> <repository-url>" and fetch from them by "git fetch --all"
# Final monorepo will contain all branches from the first remote and master branches of all remotes will be merged
# If subdirectory is not specified remote name will be used instead
#
# Usage: monorepo_build <remote-name>[:<subdirectory>] <remote-name>[:<subdirectory>] ...
#
# Example: monorepo_build main-repository package-alpha:packages/alpha package-beta:packages/beta
monorepo_build() {
    # Check provided arguments
    if [ "$#" \< "2" ]; then
        echo 'Please provide at least 2 remotes to be merged into a new monorepo'
        echo 'Usage: monorepo_build <remote-name>[:<subdirectory>] <remote-name>[:<subdirectory>] ...'
        echo 'Example: monorepo_build main-repository package-alpha:packages/alpha package-beta:packages/beta'
        return
    fi
    # Wipe original refs (possible left-over back-up after rewriting git history)
    original_refs_wipe
    for PARAM in $@; do
        # Parse parameters in format <remote-name>[:<subdirectory>]
        PARAM_ARR=(${PARAM//:/ })
        REMOTE=${PARAM_ARR[0]}
        SUBDIRECTORY=${PARAM_ARR[1]}
        if [ "$SUBDIRECTORY" == "" ]; then
            SUBDIRECTORY=$REMOTE
        fi
        # Rewrite all branches from the first remote, only master branches from others
        if [ "$PARAM" == "$1" ]; then
            echo "Building all branches of the remote '$REMOTE'"
            load_branches_from_remote $REMOTE
            rewrite_history_into $SUBDIRECTORY --branches
            MERGE_REFS='master'
        else
            echo "Building branch 'master' of the remote '$REMOTE'"
            git checkout --detach $REMOTE/master
            rewrite_history_into $SUBDIRECTORY
            MERGE_REFS="$MERGE_REFS $(git rev-parse HEAD)"
        fi
        # Wipe the back-up of original history
        original_refs_wipe
    done
    # Merge all master branches
    git checkout master
    echo "Merging refs: $MERGE_REFS"
    git merge --no-commit -q $MERGE_REFS --allow-unrelated-histories
    echo 'Resolving conflicts using trees of all parents'
    git read-tree $MERGE_REFS
    git commit -m 'merge multiple repositories into a monorepo' -m "- merged using: 'monorepo_build $@'"
    git reset --hard
}

# Split monorepo and push all master branches into specified remotes
# You must first build the monorepo via "monorepo_build" (uses same parameters as "monorepo_split")
# If subdirectory is not specified remote name will be used instead
#
# Usage: monorepo_split <remote-name>[:<subdirectory>] <remote-name>[:<subdirectory>] ...
#
# Example: monorepo_split main-repository package-alpha:packages/alpha package-beta:packages/beta
monorepo_split() {
    # Check provided arguments
    if [ "$#" \< "2" ]; then
        echo 'Please provide at least 2 remotes for splitting'
        echo 'Usage: monorepo_split <remote-name>[:<subdirectory>] <remote-name>[:<subdirectory>] ...'
        echo 'Example: monorepo_split main-repository package-alpha:packages/alpha package-beta:packages/beta'
        return
    fi
    # Wipe original refs (possible left-over back-up after rewriting git history)
    original_refs_wipe
    for PARAM in $@; do
        # Parse parameters in format <remote-name>[:<subdirectory>]
        PARAM_ARR=(${PARAM//:/ })
        REMOTE=${PARAM_ARR[0]}
        SUBDIRECTORY=${PARAM_ARR[1]}
        if [ "$SUBDIRECTORY" == "" ]; then
            SUBDIRECTORY=$REMOTE
        fi
        # Rewrite git history of master branch
        echo "Splitting repository for the remote '$REMOTE'"
        git checkout master
        rewrite_history_from $SUBDIRECTORY
        echo "Pushing branch 'master' into '$REMOTE'"
        git push $REMOTE master
        # Restore the original history from back-up
        original_refs_restore
    done
}

