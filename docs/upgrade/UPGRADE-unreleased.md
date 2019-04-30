# [Upgrade from v7.1.0 to Unreleased]

This guide contains instructions to upgrade from version v7.1.0 to Unreleased.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]
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
- if you extended one of these form fields listed below, you need to change group from `basicInformation` to `prices` ([#956](https://github.com/shopsys/shopsys/pull/956))
    - in `PaymentFormType` fields `vat` and `czkRounding`
    - in `TransportFormType` field `vat`
- change all occurences of `->will($this->returnValue(` into `->willReturn(` in all `TestCase` tests ([#939](https://github.com/shopsys/shopsys/pull/939))
    - example:
        ```diff
        - $emMock->expects($this->once())->method('find')->will($this->returnValue($expectedObject));
        + $emMock->expects($this->once())->method('find')->willReturn($expectedObject);
        ```
- remove unused dataProvider annotation from `Tests\ShopBundle\Functional\Twig\PriceExtensionTest:checkPriceFilter` method ([#939](https://github.com/shopsys/shopsys/pull/939))
    ```diff
      /**
    -   * @dataProvider priceFilterDataProvider
        * @param mixed $input
    ```
- reconfigure fm_elfinder to use main_filesystem ([#932](https://github.com/shopsys/shopsys/pull/932))
    - upgrade version of `helios-ag/fm-elfinder-bundle` to `^9.2` in `composer.json`
    - remove `barryvdh/elfinder-flysystem-driver": "^0.2"` from `composer.json`
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
    - read the section about proxying the URL content subpaths via webserver domain [`docs/introduction/abstract-filesystem.md`](https://github.com/shopsys/shopsys/blob/master/docs/introduction/abstract-filesystem.md)

### Configuration
 - use standard format for redis prefixes ([#928](https://github.com/shopsys/shopsys/pull/928))
    - change prefixes in `app/config/packages/snc_redis.yml` and `app/config/packages/test/snc_redis.yml`. Please find inspiration in [#928](https://github.com/shopsys/shopsys/pull/928/files)
    - once you finish this change, you still should deal with older redis cache keys that don't use new prefixes. Such keys are not removed even by `clean-redis-old`, please find and remove them manually (via console or UI)

    **Be careful, this upgrade will remove sessions**
- in order to have translations extracted even from overwritten templates update your `build-dev.xml` file accordingly ([#931](https://github.com/shopsys/shopsys/pull/931)):
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
- update your [nginx.conf](../../project-base/docker/nginx/nginx.conf) file like this to have in nginx the same limit for file size as for php from [php.ini](../../project-base/docker/php-fpm/php-ini-overrides.ini) ([#947](https://github.com/shopsys/shopsys/pull/947))
    ```diff
    server {
        listen 8080;
        access_log /var/log/nginx/shopsys-framework.access.log;
        root /var/www/html/web;
        server_tokens off;
    +   client_max_body_size 32M;
    ```
    - update your [ingress.yml](../../project-base/kubernetes/ingress.yml) config file
        ```diff
        metadata:
            name: shopsys
        +   annotations:
        +       nginx.ingress.kubernetes.io/proxy-body-size: 32m
        spec:
            rules:
        ```
    - check and update also all parent proxy servers for each project
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

[Upgrade from v7.1.0 to Unreleased]: https://github.com/shopsys/shopsys/compare/v7.1.0...HEAD
[shopsys/framework]: https://github.com/shopsys/framework
