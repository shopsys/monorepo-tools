# [Upgrade from v7.0.0-alpha5 to v7.0.0-alpha6](https://github.com/shopsys/shopsys/compare/v7.0.0-alpha5...v7.0.0-alpha6)

This guide contains instructions to upgrade from version v7.0.0-alpha5 to v7.0.0-alpha6.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

*Note: instructions marked as "low priority" are not vital, however, we recommend to perform them as well during upgrading as it might ease your work in the future.*

## [shopsys/framework]
- check for usages of `TransportEditFormType` - it was removed and all it's attributes were moved to `TransportFormType` so use this form instead
- check for usages of `PaymentEditFormType` - it was removed and all it's attributes were moved to `PaymentFormType` so use this form instead
- check for usages of `ProductEditFormType` - it was removed and all it's attributes were moved to `ProductFormType` so use this form instead
- pay attention to javascripts bound to your forms as well as the elements' [names and ids has changed #428](https://github.com/shopsys/shopsys/pull/428)
    - e.g. change id from `#product_edit_form_productData` to `#product_form`
    - check also your tests, you need to change names and ids of elements too
- PHP-FPM and microservice containers now expect a GitHub OAuth token set via a build argument, so it is not necessary to provide it every time those containers are rebuilt
    - see the `github_oauth_token` argument setting in the [`docker-compose.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha6/project-base/docker/conf/docker-compose.yml.dist#L33) template you used and replicate it in your `docker-compose.yml`
        - *since `docker-compose.yml` is not versioned, apply changes also in your `docker-compose.yml.dist` templates so it is easier to upgrade for your team members or for server upgrade*
    - replace the `place-your-token-here` string by the token generated on [Github -> Settings -> Developer Settings -> Personal access tokens](https://github.com/settings/tokens/new?scopes=repo&description=Composer+API+token)
- as there were changes in the Dockerfiles, replace `php-fpm` dockerfile by a new version:
    - copy [`docker/php-fpm/Dockerfile`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha6/project-base/docker/php-fpm/Dockerfile) from github
    - rebuild images `docker-compose up -d --build`
    - if you are in monorepo with microservices, just run `docker-compose up -d --build`
- [#438 - Attribute telephone moved from a billing address to the personal data of a user](https://github.com/shopsys/shopsys/pull/438)
    - this change can affect your extended forms and entities, reflect this change into your project

## [shopsys/project-base]
- [Microservice Product Search Export](https://github.com/shopsys/microservice-product-search-export) was added and it needs to be installed and run
    - check changes in the `docker-compose.yml` template you used and replicate them, there is a new container `microservice-product-search-export`
    - `parameters.yml.dist` contains new parameter `microservice_product_search_export_url`
        - add `microservice_product_search_export_url: 'http://microservice-product-search-export:8000'` into your `parameters.yml.dist`
        - execute `composer install` *(it will copy parameter into `parameters.yml`)*
- *(low priority)* instead of building the Docker images of the microservices yourself, you can use pre-built images on Docker Hub (see the [`docker-compose.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha6/project-base/docker/conf) template you used)
- [#438 - Attribute telephone moved from a billing address to the personal data of a user](https://github.com/shopsys/shopsys/pull/438)
    - edit `ShopBundle/Form/Front/Customer/BillingAddressFormType` - remove `telephone`
    - edit `ShopBundle/Form/Front/Customer/UserFormType` - add `telephone`
    - edit twig templates and tests in such a way as to reflect the movement of `telephone` attribute according to the [pull request](https://github.com/shopsys/shopsys/pull/438)
- *(low priority)* to use custom postgres configuration check changes in the [`docker-compose.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha6/project-base/docker/conf) templates and replicate them, there is a new volume for `postgres` container
    - PR [Improve Postgres configuration to improve performance](https://github.com/shopsys/shopsys/pull/444)
    - Stop running containers `docker-compose down`
    - Move data from `project-base/var/postgres-data` into `project-base/var/postgres-data/pgdata`. The directory must have correct permission depending on your OS.
      To provide you with a better image of what exactly needs to be done, there are instructions for Ubuntu:
        - `sudo su`
        - `cd project-base/var/postgres-data/`
        - trick to create directory `pgdata` with correct permissions
            - `cp -rp base/ pgdata`
            - `rm -fr pgdata/*`
        - `shopt -s extglob dotglob`
        - `mv !(pgdata) pgdata`
        - `shopt -u dotglob`
        - `exit`
    - Start containers `docker-compose up -d`
- *(low priority)* configuration files (`config.yml`, `config_dev.yml`, `config_test.yml`, `security.yml` and `wysiwyg.yml`) has been split into packages config files, for details [see #449](https://github.com/shopsys/shopsys/pull/449)
    - extract each section into own config file
        - eg. from `config.yml` extract `doctrine:` section into file `packages/doctrine.yml`
        - eg. from `config_dev.yml` extract `assetic:` section info file `packages/dev/assetic.yml`
        - and also split `wysiwyg.yml` into `packages/*.yml`
            - *(since `config.yml` will include all files in `packages/`, splitted `wysiwyg.yml` will be included automatically)*
    - move `security.yml` to `packages/security.yml`
    - the only thing that have to be left in the original configuration files is the import of these new configuration files
        - eg. `config_dev.yml` will contain only
            ```
            imports:
                 - { resource: packages/dev/*.yml }
            ```
- phing targets and console commands for working with elasticsearch were renamed, so rename them in `build.xml`, `build-dev.xml`. Also if you call them from other places, rename calling too:
    - phing targets:
        - `elasticsearch-indexes-create` -> `microservice-product-search-create-structure`
        - `elasticsearch-indexes-delete` -> `microservice-product-search-delete-structure`
        - `elasticsearch-indexes-recreate` -> `microservice-product-search-recreate-structure`
        - `elasticsearch-products-export` -> `microservice-product-search-export-products`
    - console commands:
        - `shopsys:elasticsearch:create-indexes` -> `shopsys:microservice:product-search:create-structure`
        - `shopsys:elasticsearch:delete-indexes` -> `shopsys:microservice:product-search:delete-structure`
        - `shopsys:elasticsearch:export-products` -> `shopsys:microservice:product-search:export-products`
- run `php phing ecs-fix` to apply new coding standards - [keep class spacing consistent #384](https://github.com/shopsys/shopsys/pull/384)

## [shopsys/shopsys]
- when upgrading your installed [monorepo](docs/introduction/monorepo.md), you'll have to change the build context for the images of the microservices in `docker-compose.yml`
    - `build.context` should be the root of the microservice (eg. `microservices/product-search-export`)
    - `build.dockerfile` should be `docker/Dockerfile`
    - execute `docker-compose up -d --build`, microservices should be up and running

[shopsys/framework]: https://github.com/shopsys/framework
[shopsys/project-base]: https://github.com/shopsys/project-base
[shopsys/shopsys]: https://github.com/shopsys/shopsys
