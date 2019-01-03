# Upgrading monorepo

Typical upgrade sequence should be:
* when you update your `docker-compose.yml`, you need to apply the changes by using command `docker-compose up -d`
* *(Windows, MacOS only)* any changes in `docker-sync.yml` file should follow with `docker-sync stop`, `docker-sync clean` and `docker-sync start` to restart synchronization
* run `php phing composer-dev clean db-migrations` in `php-fpm` container
* if you're experiencing some errors, you can always rebuild application and load demo data with `php phing build-demo-dev`

## [From v7.0.0-beta4 to Unreleased](https://github.com/shopsys/shopsys/compare/v7.0.0-beta4...HEAD)
- [#651 It's possible to add index prefix to elastic search](https://github.com/shopsys/shopsys/pull/651)
    - either rebuild your Docker images with `docker-compose up -d --build` or add `ELASTIC_SEARCH_INDEX_PREFIX=''` to your `.env` files in the microservice root directories, otherwise all requests to the microservices will throw `EnvNotFoundException`
- [#679 webserver container starts after php-fpm is started](https://github.com/shopsys/shopsys/pull/679)
    - add `depends_on: [php-fpm]` into `webserver` service of your `docker-compose.yml` file so webserver will not fail on error `host not found in upstream php-fpm:9000`

## [From 7.0.0-beta2 to v7.0.0-beta3](https://github.com/shopsys/shopsys/compare/v7.0.0-beta2...v7.0.0-beta3)
- *(MacOS only)* [#503 updated docker-sync configuration](https://github.com/shopsys/shopsys/pull/503/)
    - run `docker-compose down` to turn off your containers
    - run `docker-sync clean` so your volumes will be removed
    - remove these lines from `docker-compose.yml`
        ```yaml
        shopsys-framework-postgres-data-sync:
            external: true
        shopsys-framework-elasticsearch-data-sync:
            external: true
        ```
    - remove these lines from `docker-sync.yml`
        ```yaml
        shopsys-framework-postgres-data-sync:
            src: './project-base/var/postgres-data/'
            host_disk_mount_mode: 'cached'
         shopsys-framework-elasticsearch-data-sync:
            src: './project-base/var/elasticsearch-data/'
            host_disk_mount_mode: 'cached'
        ```
    - add `shopsys-framework-microservice-product-search-sync` and `shopsys-framework-microservice-product-search-export-sync` volumes to `docker-compose.yml` for `php-fpm` service
        ```yaml
        services:
            # ...
            php-fpm:
                # ...
                volumes:
                    # ...
                    - shopsys-framework-microservice-product-search-sync:/var/www/html/microservices/product-search
                    - shopsys-framework-microservice-product-search-export-sync:/var/www/html/microservices/product-search-export
        ```
    - run `docker-sync start` to create volumes
    - run `docker-compose up -d --force-recreate` to start application again
- [#533 main php-fpm container now uses multi-stage build feature](https://github.com/shopsys/shopsys/pull/533)
    - update the build config in `docker-compose.yml` ([changes in version and build config can be seen in the PR](https://github.com/shopsys/shopsys/pull/533/files#diff-1aa104f9fc120d0743883a5ba02bfe21))
    - rebuild images by running `docker-compose up -d --build`
- [#530 - Update of installation for production via docker](https://github.com/shopsys/shopsys/pull/530)
    - update `docker-compose.yml` on production server with the new configuration from updated [`docker-compose.prod.yml`](/project-base/docker/conf/docker-compose.prod.yml.dist) file
- [#545 - Part of the application build is now contained in the build of the image](https://github.com/shopsys/shopsys/pull/545)
    - rebuild image by running `docker-compose up -d --build`
- [#547 - content-test directory is used instead of content during the tests](https://github.com/shopsys/shopsys/pull/547)
    - modify your `parameters_test.yml` according to this pull request so there will be used different directory for feeds, images, etc., during the tests
- [#580 Removed trailing whitespaces from markdown files ](https://github.com/shopsys/shopsys/pull/580)
    - run `docker-compose down` to turn off your containers
    - *(MacOS, Windows only)*
        - run `docker-sync clean` so your volumes will be removed
        - remove excluding of `docs` folder from `docker-sync.yml`
        - run `docker-sync start` to create volumes
    - run `docker-compose up -d --build --force-recreate` to start application  
- *(optional)* [#551 - github token erase](https://github.com/shopsys/shopsys/pull/551)
    - you can stop providing the `github_oauth_token` in your `docker-compose.yml`

## [From 7.0.0-alpha5 to 7.0.0-alpha6](https://github.com/shopsys/shopsys/compare/v7.0.0-alpha5...v7.0.0-alpha6)
- when upgrading your installed [monorepo](/docs/introduction/monorepo.md), you'll have to change the build context for the images of the microservices in `docker-compose.yml`
    - `build.context` should be the root of the microservice (eg. `microservices/product-search-export`)
    - `build.dockerfile` should be `docker/Dockerfile`
    - execute `docker-compose up -d --build`, microservices should be up and running
