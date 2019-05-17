# [Upgrade from v7.1.0 to v7.2.0](https://github.com/shopsys/shopsys/compare/v7.1.0...v7.2.0)

This guide contains instructions to upgrade from version v7.1.0 to v7.2.0.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]

### Infrastructure
- update the definition of the postgres service in your `docker-compose.yml` file to use the customized configuration ([#946](https://github.com/shopsys/shopsys/pull/946))
    ```diff
    postgres:
        image: postgres:10.5-alpine
        container_name: shopsys-framework-postgres
        volumes:
            - ./docker/postgres/postgres.conf:/var/lib/postgresql/data/postgresql.conf:delegated
            - ./var/postgres-data:/var/lib/postgresql/data:cached
        environment:
            - PGDATA=/var/lib/postgresql/data/pgdata
            - POSTGRES_USER=root
            - POSTGRES_PASSWORD=root
            - POSTGRES_DB=shopsys
    +   command:
    +       - postgres
    +       - -c
    +       - config_file=/var/lib/postgresql/data/postgresql.conf
    ```
    - similarly, update postgres deployment manifest in your `kubernetes/deployments/postgres.yml`
        ```diff
                -   name: PGDATA
                    value: /var/lib/postgresql/data/pgdata
        + args:
        +    - postgres
        +    - -c
        +    - config_file=/var/lib/postgresql/data/postgresql.conf
        ```
- update your [nginx.conf](../../project-base/docker/nginx/nginx.conf) file like this to have in nginx the same limit for file size as for php from [php.ini](../../project-base/docker/php-fpm/php-ini-overrides.ini) ([#947](https://github.com/shopsys/shopsys/pull/947))
    ```diff
    server {
        listen 8080;
        access_log /var/log/nginx/shopsys-framework.access.log;
        root /var/www/html/web;
        server_tokens off;
    +   client_max_body_size 32M;
    ```
    - also, update your [ingress.yml](../../project-base/kubernetes/ingress.yml) config file
        ```diff
        metadata:
            name: shopsys
        +   annotations:
        +       nginx.ingress.kubernetes.io/proxy-body-size: 32m
        spec:
            rules:
        ```
    - check and update also all parent proxy servers for each project
- remove node ports from kubernetes services and add them into the ingress router for the CI overlay ([#888](https://github.com/shopsys/shopsys/pull/888))
    - remove `NodePort` type from `kubernetes/services/adminer.yml`, `kubernetes/services/elasticsearch.yml` and `kubernetes/services/redis-admin.yml`
        ```diff
        -     type: NodePort
          ports:
          -   name: http
        ```
    - add ingress kustomize patch for CI deployment into `kubernetes/kustomize/overlays/ci/kustomization.yaml`
        ```diff
          -   ../../../services/selenium-server.yml
        +patchesJson6902:
        +-   target:
        +        group: extensions
        +        version: v1beta1
        +        kind: Ingress
        +        name: shopsys
        +    path: ./ingress-patch.yaml
          configMapGenerator:
        ```
    - create `kubernetes/kustomize/overlays/ci/ingress-patch.yaml` and add the routes for adminer, elasticsearch and redis-admin
        ```diff
        +- op: add
        +  path: /spec/rules/-
        +  value:
        +    host: ~
        +    http:
        +        paths:
        +        -   path: /
        +            backend:
        +                serviceName: adminer
        +                servicePort: 80
        +- op: add
        +  path: /spec/rules/-
        +  value:
        +    host: ~
        +    http:
        +        paths:
        +        -   path: /
        +            backend:
        +                serviceName: elasticsearch
        +                servicePort: 9200
        +- op: add
        +  path: /spec/rules/-
        +  value:
        +    host: ~
        +    http:
        +        paths:
        +        -   path: /
        +            backend:
        +                serviceName: redis-admin
        +                servicePort: 80
        ```

### Application
- add `TransformString::removeDriveLetterFromPath` transformer for all absolute paths that could be based with drive letter file systems and used by `local_filesystem` service ([#942](https://github.com/shopsys/shopsys/pull/942))
    - add TransformString::removeDriveLetterFromPath into `src/Shopsys/ShopBundle/DataFixtures/Demo/ImageDataFixture.php`
        ```diff
        protected function moveFilesFromLocalFilesystemToFilesystem(string $origin, string $target)
        {
            $finder = new Finder();
            $finder->files()->in($origin);
            foreach ($finder as $file) {
        -        $filepath = $file->getPathname();
        +        $filepath = TransformString::removeDriveLetterFromPath($file->getPathname());
        ```
- if you extended one of these form fields listed below, you need to change the group from `basicInformation` to `prices` ([#956](https://github.com/shopsys/shopsys/pull/956))
    - in `PaymentFormType` fields `vat` and `czkRounding`
    - in `TransportFormType` field `vat`
- change all occurrences of `->will($this->returnValue(…))` into `->willReturn(…)` in all your `TestCase` tests ([#939](https://github.com/shopsys/shopsys/pull/939))
    - example:
        ```diff
        - $emMock->expects($this->once())->method('find')->will($this->returnValue($expectedObject));
        + $emMock->expects($this->once())->method('find')->willReturn($expectedObject);
        ```
- remove unused `@dataProvider` annotation from `Tests\ShopBundle\Functional\Twig\PriceExtensionTest:checkPriceFilter` method ([#939](https://github.com/shopsys/shopsys/pull/939))
    ```diff
      /**
    -   * @dataProvider priceFilterDataProvider
        * @param mixed $input
    ```
- reconfigure `fm_elfinder` to use `main_filesystem` ([#932](https://github.com/shopsys/shopsys/pull/932))
    - upgrade the version of `helios-ag/fm-elfinder-bundle` composer dependency to `^9.2`
        - you can do this by `composer require helios-ag/fm-elfinder-bundle:^9.2 --update-with-dependencies`
    - remove the package `barryvdh/elfinder-flysystem-driver` from your direct composer dependecies (`shopsys/framework` includes the driver implementation)
        - you can do this by `composer remove barryvdh/elfinder-flysystem-driver`
    - update `fm_elfinder.yml` config
        ```diff
            driver: Flysystem
        -   path: '%shopsys.filemanager_upload_web_dir%'
        +   path: 'web/%shopsys.filemanager_upload_web_dir%'
            flysystem:
        -       type: local
        -       options:
        -           local:
        -               path: '%shopsys.web_dir%'
        +       enabled: true
        +       filesystem: 'main_filesystem'
            upload_allow: ['image/png', 'image/jpg', 'image/jpeg']
        -   tmb_path: '%shopsys.filemanager_upload_web_dir%/_thumbnails'
        +   tmb_path: 'web/%shopsys.filemanager_upload_web_dir%/_thumbnails'
            url: '%shopsys.filemanager_upload_web_dir%'
            tmb_url: '%shopsys.filemanager_upload_web_dir%/_thumbnails'
            attributes:
                thumbnails:
        -           pattern: '/^\/content\/wysiwyg\/_thumbnails$/'
        +           pattern: '/^\/web\/content\/wysiwyg\/_thumbnails$/'
                    hidden: true
        ```
    - read the section about proxying the URL content subpaths via webserver domain in the [`Abstract Filesystem documentation`](https://github.com/shopsys/shopsys/blob/v7.2.0/docs/introduction/abstract-filesystem.md#create-nginx-proxy-to-load-files-from-different-storage)
- to be more descriptive about the error caused by active TEST environment, modify `ErrorController::createUnableToResolveDomainResponse()` to be explicit about `overwrite_domain_url` parameter ([#701](https://github.com/shopsys/shopsys/pull/701))
    - you can [see the diff](https://github.com/shopsys/project-base/commit/4d80864be1809ada9a86f49912b79a562360e3f3)
- use interchangeable product filtering ([#943](https://github.com/shopsys/shopsys/pull/943))
    - you'll find detailed instructions in separate article [Upgrade Instructions for Interchangeable Filtering](/docs/upgrade/interchangeable-filtering.md)

### Configuration
 - use the standard format for redis prefixes ([#928](https://github.com/shopsys/shopsys/pull/928))
    - change the prefixes in `app/config/packages/snc_redis.yml` and `app/config/packages/test/snc_redis.yml` - please find inspiration in [the diff of #928](https://github.com/shopsys/project-base/commit/7f68a0a94fae07ade52c2aed4ce611926bf7403b)
    - once you finish this change, you should still deal with older redis cache keys that don't use new prefixes - such keys are not removed even by `php phing clean-redis-old`, please find and remove them manually (via console or UI)

    **Be careful, this upgrade will remove current sessions**
- use redis as cache for doctrine and framework ([#930](https://github.com/shopsys/shopsys/pull/930))
    - update `app/config/packages/framework.yml`:
        ```diff
        framework:
        +    annotations:
        +        cache: shopsys.framework.cache_driver.annotations_cache
        ```
    - update `app/config/packages/snc_redis.yml`:
        ```diff
        snc_redis:
            clients:
                ...
        +       framework_annotations:
        +           type: 'phpredis'
        +           alias: 'framework_annotations'
        +           dsn: 'redis://%redis_host%'
        +           options:
        +               prefix: '%env(REDIS_PREFIX)%%build-version%:cache:framework:annotations:'
        ```
    - update `app/config/packages/doctrine.yml`:
        ```diff
        metadata_cache_driver:
            type: service
        -   id: Doctrine\Common\Cache\ChainCache
        +   id: shopsys.doctrine.cache_driver.metadata_cache
        query_cache_driver:
            type: service
        -   id: Doctrine\Common\Cache\ChainCache
        +   id: shopsys.doctrine.cache_driver.query_cache
        ```
    - update `app/config/packages/test/doctrine.yml`:
        ```diff
        doctrine:
            ...
        +   orm:
        +       metadata_cache_driver:
        +           type: service
        +           id: Doctrine\Common\Cache\ArrayCache
        +       query_cache_driver:
        +           type: service
        +           id: Doctrine\Common\Cache\ArrayCache
        ```
    - update `app/config/packages/dev/doctrine.yml`:
        ```diff
        doctrine:
            orm:
                auto_generate_proxy_classes: true
        -       metadata_cache_driver: array
        -       query_cache_driver: array
        ```
- prepare the configuration file for cron services in your project repository ([#989](https://github.com/shopsys/shopsys/pull/989))
    - create a `cron.yml` file in `src/ShopBundle/Resources/config/services/` (if you created the `cron.yml` already, move it there)
    - insert the following code as a template:
        ```
        services:
            _defaults:
                autowire: true
                autoconfigure: true
                public: false

        #   Example:
        #   Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExportCronModule:
        #       tags:
        #           - { name: shopsys.cron, hours: '*', minutes: '*' }
        ```
    - update `src/Shopsys/ShopBundle/Resources/config/services.yml` to import the service definitions:
        ```diff
        imports:
            - { resource: forms.yml }
        -   - { resource: services/commands.yml }
        -   - { resource: services/data_fixtures.yml }
        +   - { resource: services/*.yml }
        ```
- move `database_server_version` parameter to `parameters_common.yml` ([#1001](https://github.com/shopsys/shopsys/pull/1001))
    - remove parameter from `parameters.yml` and `parameters.yml.dist`
    - add the parameter to `parameters_common.yml`:
        ```diff
        parameters:
            database_driver: pdo_pgsql
        +   database_server_version: 10.5
        ...
        ```
- add `env(ELASTIC_SEARCH_INDEX_PREFIX): ''` into your `app/config/parameters.yml.dist` and also `app/config/parameters.yml` ([#961](https://github.com/shopsys/shopsys/pull/961))

### Tools
- add path for tests folder into `ecs-fix` phing target of `build-dev.xml` file to be able to fix files that were found by `ecs` phing target ([#980](https://github.com/shopsys/shopsys/pull/980))
    ```diff
      <arg path="${path.src}" />
    + <arg path="${path.tests}" />
    ```
- in order to have translations extracted even from overwritten templates, update your `build-dev.xml` file ([#931](https://github.com/shopsys/shopsys/pull/931)):
    ```diff
        <target name="dump-translations-project-base" description="Extracts translatable messages from all source files in project base.">
            <exec executable="${path.php.executable}" passthru="true" checkreturn="true">
                <arg value="${path.bin-console}" />
                <arg value="translation:extract" />
                <arg value="--bundle=ShopsysShopBundle" />
                <arg value="--dir=${path.src}/Shopsys/ShopBundle" />
    +           <arg value="--dir=${path.app}/Resources" />
                <arg value="--exclude-dir=frontend/plugins" />
                <arg value="--output-format=po" />
                <arg value="--output-dir=${path.src}/Shopsys/ShopBundle/Resources/translations" />
                <arg value="--keep" />
                <arg value="cs" />
                <arg value="en" />
            </exec>
        </target>
    ```

[shopsys/framework]: https://github.com/shopsys/framework
