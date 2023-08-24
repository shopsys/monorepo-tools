#!/usr/bin/env bash

# Build monorepo from specified remotes
# You must first add the remotes by "git remote add <remote-name> <repository-url>" and fetch from them by "git fetch --all"
# Final monorepo will contain all branches from the first remote and master branches of all remotes will be merged
# If subdirectory is not specified remote name will be used instead
#
# Usage: monorepo_build.sh <remote-name>[:<subdirectory>] <remote-name>[:<subdirectory>] ...
#
# Example: monorepo_build.sh main-repository package-alpha:packages/alpha package-beta:packages/beta

# Check provided arguments
if [ "$#" -lt "2" ]; then
    echo 'Please provide at least 2 remotes to be merged into a new monorepo'
    echo 'Usage: monorepo_build.sh <remote-name>[:<subdirectory>] <remote-name>[:<subdirectory>] ...'
    echo 'Example: monorepo_build.sh main-repository package-alpha:packages/alpha package-beta:packages/beta'
    read -p "Do you want to proceed? (y/n) " yn
    case $yn in 
    	[yY] ) echo Proceeding;
    		break;;
    	* ) echo exiting...;
    		exit;;
    esac
fi
# Get directory of the other scripts
MONOREPO_SCRIPT_DIR=$(dirname "$0")
# Wipe original refs (possible left-over back-up after rewriting git history)
$MONOREPO_SCRIPT_DIR/original_refs_wipe.sh
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
        $MONOREPO_SCRIPT_DIR/load_branches_from_remote.sh $REMOTE
        $MONOREPO_SCRIPT_DIR/rewrite_history_into.sh $SUBDIRECTORY --branches
        MERGE_REFS='master'
    else
        echo "Building branch 'master' of the remote '$REMOTE'"
        git checkout --detach $REMOTE/master
        $MONOREPO_SCRIPT_DIR/rewrite_history_into.sh $SUBDIRECTORY
        MERGE_REFS="$MERGE_REFS $(git rev-parse HEAD)"
    fi
    # Wipe the back-up of original history
    $MONOREPO_SCRIPT_DIR/original_refs_wipe.sh
done
# Merge all master branches
COMMIT_MSG="merge multiple repositories into a monorepo"$'\n'$'\n'"- merged using: 'monorepo_build.sh $@'"$'\n'"- see https://github.com/shopsys/monorepo-tools"
git checkout master
echo "Merging refs: $MERGE_REFS"
git merge --no-commit -q $MERGE_REFS --allow-unrelated-histories
echo 'Resolving conflicts using trees of all parents'
for REF in $MERGE_REFS; do
    # Add all files from all master branches into index
    # "git read-tree" with multiple refs cannot be used as it is limited to 8 refs
    git ls-tree -r $REF | git update-index --index-info
done
git commit -m "$COMMIT_MSG"
git reset --hard

