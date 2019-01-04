# [Upgrade from 7.0.0-alpha4 to 7.0.0-alpha5](https://github.com/shopsys/shopsys/compare/v7.0.0-alpha4...v7.0.0-alpha5)

This guide contains instructions to upgrade from version 7.0.0-alpha4 to 7.0.0-alpha5.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]
- for [product search via Elasticsearch](/docs/introduction/product-search-via-elasticsearch.md), you'll have to:
    - check changes in the [`docker-compose.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/docker/conf) template you used and replicate them, there is a new container with Elasticsearch
        - *since `docker-compose.yml` is not versioned, apply changes also in your `docker-compose.yml.dist` templates so it is easier to upgrade for your team members or for server upgrade*
    - since the fully installed and ready [Microservice Product Search](https://github.com/shopsys/microservice-product-search) is a necessary condition for the Shopsys Framework to run, the installation procedure of this microservice is a part of Shopsys Framework [installation guide](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/docs/installation/installation-using-docker-application-setup.md)
        - alternately you can use [docker microservice image](https://github.com/shopsys/demoshop/blob/4946be4111d7fae4d7497921f9a4ec9aed24db42/docker/conf/docker-compose.yml.dist#L104-L110) that require no installation
    - run `docker-compose up -d`
    - update composer dependencies `composer update`
    - create Elasticsearch indexes by running `php phing elasticsearch-indexes-create`
    - export products into Elasticsearch by `php phing elasticsearch-products-export`
- `ProductFormType` [is extensible now #375](https://github.com/shopsys/shopsys/pull/375). If you extended the product form, you have to:
    - move form parts into right subsections, eg. [this change on demoshop](https://github.com/shopsys/demoshop/commit/62ae3dd3f2880f4c0d2a5ec33747c3f2f8448f41)
    - if you don't have custom rendering, remove your template for form
    - if you have custom rendering, change rendering of these parts as they are now in subsections
    - as the form changed structure, you have to also fix tests. see [this change on demoshop](https://github.com/shopsys/demoshop/commit/62ae3dd3f2880f4c0d2a5ec33747c3f2f8448f41)
        - form fields changed names and also ids

### PostgreSQL upgrade:
We decided to move onto a newer version of PostgreSQL.

These steps are for migrating your data onto newer version of postgres and are inspired by [official documentation](https://www.postgresql.org/docs/10/static/upgrading.html):

If you are running your project natively then just follow [official instructions](https://www.postgresql.org/docs/10/static/upgrading.html),
if you are using docker infrastructure you can follow steps written below.

1. create a backup of your database by executing::

    `docker exec -it shopsys-framework-postgres pg_dumpall > backupfile`

1. apply changes in `docker-compose.yml`, you can find them in a new version of [`docker-compose.yml.dist`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/docker/conf) templates

    *Note: select correct `docker-compose` according to your operating system*

    *since `docker-compose.yml` is not versioned, apply changes also in your `docker-compose.yml.dist` templates so it is easier to upgrade for your team members or for server upgrade*

1. update version of `database_server_version` from *9.5* to *10.5* in your `parameters.yml`

1. stop containers and delete old data:

    `docker-compose down`

    `rm -rf <project-root-path>/var/postgres-data/*`

1. use a new version of `php-fpm` container:

    `curl -L https://github.com/shopsys/shopsys/raw/v7.0.0-alpha5/project-base/docker/php-fpm/Dockerfile --output docker/php-fpm/Dockerfile`

    `docker-compose build php-fpm`

1. start new docker-compose stack with newer version of postgres by just recreating your containers:

    `docker-compose up -d --force-recreate`

1. copy backup into postgres container root folder

    `docker cp backupfile shopsys-framework-postgres:/`

1. restore you data:

    `docker exec -it shopsys-framework-postgres psql -d postgres -f backupfile`

1. delete backup file:

    `docker exec -it shopsys-framework-postgres rm backupfile`

1. recreate collations:

    `docker exec shopsys-framework-php-fpm ./phing db-create test-db-create`

## [shopsys/project-base]
- added [Microservice Product Search](https://github.com/shopsys/microservice-product-search)
    - check changes in the [`docker-compose.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/docker/conf) template you used and replicate them, there is a new container `microservice-product-search`
        - *since `docker-compose.yml` is not versioned, apply changes also in your `docker-compose.yml.dist` templates so it is easier to upgrade for your team members or for server upgrade*
        - follow [installation guide](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/docs/installation/installation-using-docker-application-setup.md) to install microservice
          or use [docker microservice image](https://github.com/shopsys/demoshop/blob/4946be4111d7fae4d7497921f9a4ec9aed24db42/docker/conf/docker-compose.yml.dist#L104-L110) that require no installation
    - into `parameters.yml.dist` add a new parameter `microservice_product_search_url`:
        - `microservice_product_search_url: 'http://microservice-product-search:8000'`
        - and add it also into `parameters.yml`
    - modify a configuration in `services.yml` for:
        - `Shopsys\FrameworkBundle\Model\Product\Search\ProductSearchRepository`
        - `shopsys.microservice_client.product_search`
    - remove a configuration in `services.yml` for:
        - `Shopsys\FrameworkBundle\Model\Product\Search\ElasticsearchSearchClient`
        - `Shopsys\FrameworkBundle\Model\Product\Search\CachedSearchClient`
        - `Shopsys\FrameworkBundle\Model\Product\Search\SearchClient`
- *(optional)* standardize indentation in your yaml files
    - you can find yaml files with wrong indentation with regexp `^( {4})* {1,3}[^ ]`
- *(optional)* we added a new phing target that checks [availabitliy of microservices](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/project-base/build-dev.xml#L726-L731).
  Feel free to include this target into your build process.
- add new themes to configuration `app/config/config.yml`, path `twig.form_themes`:
    ```
        - '@ShopsysFramework/Admin/Form/warningMessage.html.twig'
        - '@ShopsysFramework/Admin/Form/displayOnlyUrl.html.twig'
        - '@ShopsysFramework/Admin/Form/localizedFullWidth.html.twig'
        - '@ShopsysFramework/Admin/Form/productParameterValue.html.twig'
        - '@ShopsysFramework/Admin/Form/productCalculatedPrices.html.twig'
    ```

[shopsys/framework]: https://github.com/shopsys/framework
[shopsys/project-base]: https://github.com/shopsys/project-base
