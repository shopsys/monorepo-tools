#!/usr/bin/env bash

# Lists packages that should be split
# If you modify this list don't forget updating /docs/introduction/monorepo.md, /changelog-linker.yml, /CHANGELOG.md, and "replace" section in monorepo's composer.json as well
get_all_packages() {
    echo "framework \
        read-model \
        google-cloud-bundle \
        product-feed-zbozi \
        product-feed-google \
        product-feed-heureka \
        product-feed-heureka-delivery \
        plugin-interface \
        coding-standards \
        http-smoke-testing \
        form-types-bundle \
        migrations \
        monorepo-tools \
        project-base"
}

# Gets a subdirectory in which a package is located
get_package_subdirectory() {
    PACKAGE=$1

    if [[ "$PACKAGE" == "project-base" ]]; then
        echo $PACKAGE
    else
        echo "packages/$PACKAGE"
    fi
}

# Gets a remote into which a package should be pushed
get_package_remote() {
    PACKAGE=$1

    echo "git@github.com:shopsys/$PACKAGE.git"
}
