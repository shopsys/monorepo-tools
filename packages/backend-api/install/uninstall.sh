#!/bin/bash

INSTALL_DIR="$( cd "$(dirname "$0")" ; pwd -P )"

PWD_PROJECT_BASE_PATH=`[ -d "$PWD/project-base" ] && echo "$PWD/project-base" || echo "$PWD"`

if [ "$1" == "monorepo" ]
then
    PROJECT_BASE_PATH=`realpath ${INSTALL_DIR}/../../../project-base`
    if [ "$PWD_PROJECT_BASE_PATH" != "$PROJECT_BASE_PATH" ]
    then
        echo "You have to run uninstall.sh from monorepo root. Eg. /var/www/html"
        exit 1
    fi
else
    PROJECT_BASE_PATH=`realpath ${INSTALL_DIR}/../../../../`
    if [ "$PWD_PROJECT_BASE_PATH" != "$PROJECT_BASE_PATH" ]
    then
        echo "You have to run uninstall.sh from the project root. Eg. /var/www/html"
        exit 1
    fi
fi

rm -f ${PROJECT_BASE_PATH}/app/config/packages/fos_rest.yml

rm -f ${PROJECT_BASE_PATH}/app/config/packages/trikoder_oauth2.yml
rm -rf ${PROJECT_BASE_PATH}/app/config/packages/oauth2

rm -rf ${PROJECT_BASE_PATH}/src/Shopsys/ShopBundle/Controller/Api

rm -f ${PROJECT_BASE_PATH}/tests/ShopBundle/Smoke/BackendApiTest.php
rm -f ${PROJECT_BASE_PATH}/tests/ShopBundle/Test/OauthTestCase.php

function apply_patch_reverse () {
    if [ -z $1 ]
    then
        echo "Please provide path as first parameter of apply_patch_reverse function"
        exit 1
    fi

    local FILE_PATH=$1
    local SOURCE_FILE_PATH=$1

    if [ ! -z $2 ]
    then
        SOURCE_FILE_PATH=$2
    fi

    echo "Reverting ${FILE_PATH}..."
    local PATCH_DRY_RUN=`patch -Rf --dry-run ${PROJECT_BASE_PATH}/${FILE_PATH} ${INSTALL_DIR}/${SOURCE_FILE_PATH}.patch`
    local PATCH_FAIL=`grep "FAILED" <<< ${PATCH_DRY_RUN}`

    if [ ! -z "${PATCH_FAIL}" ]
    then
        local PATCH_APPLY_DRY_RUN=`patch -st --dry-run ${PROJECT_BASE_PATH}/${FILE_PATH} ${INSTALL_DIR}/${SOURCE_FILE_PATH}.patch`
        if [ -z "${PATCH_APPLY_DRY_RUN}" ]
        then
            echo "Applied patch detected: ${FILE_PATH} already reverted, Doing nothing"
        else
            echo ${PATCH_FAIL}
            echo "${FILE_PATH} cannot be reverted!"
            AT_LEAST_ONE_PATCH_FAILED=1
        fi
    else
        patch -Rf ${PROJECT_BASE_PATH}/${FILE_PATH} ${INSTALL_DIR}/${SOURCE_FILE_PATH}.patch
        echo "Done"
    fi
}

apply_patch_reverse "app/config/parameters_common.yml"
apply_patch_reverse "app/config/routing.yml"
apply_patch_reverse "app/config/packages/security.yml"
apply_patch_reverse "src/Shopsys/ShopBundle/Resources/config/routing.yml"
apply_patch_reverse "app/AppKernel.php"
apply_patch_reverse "build.xml"

if [ "$1" == "monorepo" ]
then
    echo "Running from monorepo, not applying patch for project-base composer.json because it has no effect in monorepo."
else
    apply_patch_reverse "composer.json"
fi

if [ -z $AT_LEAST_ONE_PATCH_FAILED ]
then
    exit 0
else
    echo "Backend API uninstallation failed!"
    exit 1
fi