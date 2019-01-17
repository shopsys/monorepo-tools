#!/usr/bin/env bash

# Removes specified previously split branches from all repositories
#
# Usage: monorepo_remove_branch.sh <branch>
#
# Example: monorepo_remove_branch.sh my-feature-branch

# ANSI color codes
RED="\e[31m"
GREEN="\e[32m"
BLUE="\e[34m"
NC="\e[0m"

SPLIT_BRANCH=$1

if [[ "$SPLIT_BRANCH" == "" ]]; then
    printf "${RED}$(date +%T) > You must provide branch name to remove!${NC}\n\n"
    exit 1
elif [[ "$SPLIT_BRANCH" == "master" ]]; then
    printf "${RED}$(date +%T) > You cannot remove master branch!${NC}\n\n"
    exit 1
else
    printf "${BLUE}$(date +%T) > Removing branch '$SPLIT_BRANCH'...${NC}\n\n"
fi

# Import functions
. $(dirname "$0")/monorepo_functions.sh

# Remove the branch from all repositories
EXIT_STATUS=0
for PACKAGE in $(get_all_packages); do
    git push --delete $(get_package_remote "$PACKAGE") $SPLIT_BRANCH

    if [[ $? -eq 0 ]]; then
        printf "${GREEN}Branch '$SPLIT_BRANCH' was removed from the package '$PACKAGE'!${NC}\n"
    else
        printf "${RED}Branch '$SPLIT_BRANCH' could not be removed from the package '$PACKAGE'!${NC}\n"
        EXIT_STATUS=1
    fi
done

if [[ $EXIT_STATUS -eq 0 ]]; then
    printf "\n${GREEN}$(date +%T) > Branches from all repositories were removed!${NC}\n\n"
else
    printf "\n${RED}$(date +%T) > Some branches were not removed from their remotes due to an error.${NC}\n\n"
fi

exit $EXIT_STATUS
