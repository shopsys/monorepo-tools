#!/usr/bin/env bash

# Lists packages that should be split
get_all_packages() {
    echo "framework \
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
        project-base \
        microservice-product-search \
        microservice-product-search-export"
}

# Gets a subdirectory in which a package is located
get_package_subdirectory() {
    PACKAGE=$1

    if [[ "$PACKAGE" == "project-base" ]]; then
        echo $PACKAGE
    elif [[ "${PACKAGE:0:13}" == "microservice-" ]]; then
        echo "microservices/${PACKAGE:13}"
    else
        echo "packages/$PACKAGE"
    fi
}

# Gets a remote into which a package should be pushed
get_package_remote() {
    PACKAGE=$1

    echo "git@github.com:shopsys/$PACKAGE.git"
}
