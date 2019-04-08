# [Upgrade from v7.0.0 to v7.1.0]

This guide contains instructions to upgrade from version v7.0.0 to v7.1.0.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]
### Application
- *(low priority)* to add support of functional tests of Redis ([#846](https://github.com/shopsys/shopsys/pull/846))
    - download [`snc_redis.yml`](https://github.com/shopsys/project-base/blob/v7.1.0/app/config/packages/test/snc_redis.yml) to `app/config/packages/test/snc_redis.yml`
    - download [`RedisFacadeTest.php`](https://github.com/shopsys/project-base/tree/v7.1.0/tests/ShopBundle/Functional/Component/Redis/RedisFacadeTest.php) to your tests directory `tests/ShopBundle/Functional/Component/Redis/`
- *(low-priority)* by changes implemented in [#808 redesign print page of product detail page](https://github.com/shopsys/shopsys/pull/808) was created new folder `print` in `src/Shopsys/ShopBundle/Resources/styles/front/common`. In the pull request were implemented changes for styling print page of initial Shopsys Framework configuration. In order to apply changes in your project there is need to do following steps.
    - copy whole `print` folders from `src/Shopsys/ShopBundle/Resources/styles/front/common/print` and `src/Shopsys/ShopBundle/Resources/styles/front/domain2/print` of [shopsys/project-base](https://github.com/shopsys/project-base/)
    - add `dont-print` class to HTML elements which you want to hide. You can inspire by [changes](https://github.com/shopsys/shopsys/pull/808/files) implemented in [pull request](https://github.com/shopsys/shopsys/pull/808).
    - add new `less` subtask in `src/Shopsys/ShopBundle/Resources/views/Grunt/gruntfile.js.twig`
    ```diff
    {% for domain in domains -%}
        helpers{{ domain.id }}: {
            files: {
                '{{ (customResourcesDirectory ~ '/styles/front/' ~ domain.stylesDirectory)|raw }}/helpers/helpers-generated.less': '{{ (customResourcesDirectory ~ '/styles/front/' ~ domain.stylesDirectory)|raw }}/helpers.less'
            }
        },
        frontend{{ domain.id }}: {
            files: {
                'web/assets/frontend/styles/index{{ domain.id }}_{{ cssVersion }}.css': '{{ (customResourcesDirectory ~ '/styles/front/' ~ domain.stylesDirectory)|raw }}/main.less'
            },
            options: {
                compress: true,
                sourceMap: true,
                sourceMapFilename: 'web/assets/frontend/styles/index{{ domain.id }}_{{ cssVersion }}.css.map',
                sourceMapBasepath: 'web',
                sourceMapURL: 'index{{ domain.id }}_{{ cssVersion }}.css.map',
                sourceMapRootpath: '../../../'
            }
        },
    +   print{{ domain.id }}: {
    +       files: {
    +           'web/assets/frontend/styles/print{{ domain.id }}_{{ cssVersion }}.css': '{{ (customResourcesDirectory ~ '/styles/front/' ~ domain.stylesDirectory)|raw }}/print/main.less'
    +       },
    +       options: {
    +           compress: true
    +       }
    +   },
    ```
    - include this new task in `frontend` and `frontendLess` tasks, so next time when you will run grunt it will generate also print styles
    ```diff
    {% for domain in domains -%}
    -   grunt.registerTask('frontend{{ domain.id }}', ['webfont:frontend', 'sprite:frontend', 'less:frontend{{ domain.id }}', 'legacssy:frontend{{ domain.id }}', 'less:wysiwyg{{ domain.id }}'], 'postcss');
    +   grunt.registerTask('frontend{{ domain.id }}', ['webfont:frontend', 'sprite:frontend', 'less:frontend{{ domain.id }}', 'less:print{{ domain.id }}', 'legacssy:frontend{{ domain.id }}', 'less:wysiwyg{{ domain.id }}'], 'postcss');
    {% endfor -%}
    grunt.registerTask('admin', ['sprite:admin', 'webfont:admin', 'less:admin', 'legacssy:admin' ]);

    {% for domain in domains -%}
    -   grunt.registerTask('frontendLess{{ domain.id }}', ['less:frontend{{ domain.id }}', 'legacssy:frontend{{ domain.id }}', 'less:wysiwyg{{ domain.id }}']);
    +   grunt.registerTask('frontendLess{{ domain.id }}', ['less:frontend{{ domain.id }}', 'legacssy:frontend{{ domain.id }}', 'less:print{{ domain.id }}', 'less:wysiwyg{{ domain.id }}']);
    {% endfor -%}
    ```
    - attach generated print CSS file in document header. In order to do that you have to modify `src/Shopsys/ShopBundle/Resources/views/Front/Layout/base.html.twig` as is shown below.
    ```diff
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/styles/index' ~ getDomain().id ~ '_' ~ getCssVersion() ~ '.css') }}" media="screen, projection">
    <!--[if lte IE 8 ]>
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/styles/index' ~ getDomain().id ~ '_' ~ getCssVersion() ~ '-ie8.css') }}" media="screen, projection">
    <![endif]-->

    + <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/styles/print' ~ getDomain().id ~ '_' ~ getCssVersion() ~ '.css') }}" media="print">
    ```
    - generate new `Grtuntfile.js` by running command `php phing gruntfile`
    - generate new CSS files by running command `php phing grunt`
- *(low priority)* add custom message for unique e-mail validation in `/src/Shopsys/ShopBundle/Form/Front/Registration/RegistrationFormType.php` ([#885](https://github.com/shopsys/shopsys/pull/885))
    ```diff
    -                    new UniqueEmail(),
    +                    new UniqueEmail(['message' => 'This e-mail is already registered']),
    ```
    - dump translations via `php phing dump-translations`
    - fix `CustomerRegistrationCest::testAlreadyUsedEmail` acceptation test
        ```diff
        -        $registrationPage->seeEmailError('Email no-reply@shopsys.com is already registered');
        +        $registrationPage->seeEmailError('This e-mail is already registered');
        ```
- *(low priority)* remove option `choice_name` from `flags` and `brands` in `ShopBundle/Form/Front/Product/ProductFilterFormType.php` ([#891](https://github.com/shopsys/shopsys/pull/891))
- create `Shopsys\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig` by extending `Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig` ([#895](https://github.com/shopsys/shopsys/pull/895))
    - register `\Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductCategoryFilter` filter via method `registerFilter` in constructor
    - register it as service alias via `ShopBundle/Resources/config/services.yml` and `ShopBundle/Resources/config/services_test.yml`
        ```diff
        +Shopsys\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig: ~
        +Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig: '@Shopsys\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig'
        ```
- *(low priority)* use private method `recursivelyCountCategoriesInCategoryTree` instead of `array_sum` to get maximum for the progress bar in `Shopsys\ShopBundle\DataFixtures\Performance\CategoryDataFixture` ([#902](https://github.com/shopsys/shopsys/pull/902))
- fix EntityExtensionTest ([899](https://github.com/shopsys/shopsys/pull/899))
    - add this code to your `setUp()` method in `tests/ShopBundle/Functional/EntityExtension/EntityExtensionTest.php`
        ```diff
            $entityExtensionMap = [
                Product::class => ExtendedProduct::class,
                Category::class => ExtendedCategory::class,
                OrderItem::class => ExtendedOrderItem::class,
                Brand::class => ExtendedBrand::class,
                Order::class => ExtendedOrder::class,
                ProductTranslation::class => ExtendedProductTranslation::class,
            ];

        +    $applicationEntityExtensionMap = $this->getContainer()->getParameter('shopsys.entity_extension.map');
        +
        +    foreach ($applicationEntityExtensionMap as $baseClass => $extendedClass) {
        +        if (!array_key_exists($baseClass, $entityExtensionMap)) {
        +            $entityExtensionMap[$baseClass] = $extendedClass;
        +        }
        +    }
        ```

### Configuration
- *(low priority)* to improve your deployment process and avoid possible Redis cache problems during deployment, include `build-version` into your builds ([#886](https://github.com/shopsys/shopsys/pull/886))
    - to `app/AppKernel.php` into function `getConfig()` add
        ```diff
        private function getConfig()
        {
            // ...
        +    if (file_exists(__DIR__ . '/config/parameters_version.yml')) {
        +        $configs[] = __DIR__ . '/config/parameters_version.yml';
        +    }

            return $configs;
        }
        ```
    - to `app/config/.gitignore` add
        ```diff
        + parameters_version.yml
        ```
    - configure redis cache clients in `app/config/packages/snc_redis.yml` to use `build-version` prefixes, eg.
        ```diff
        snc_redis:
            clients:
                bestselling_products:
                # ...
        -           prefix: '%env(REDIS_PREFIX)%bestselling_products_'
        +           prefix: '%env(REDIS_PREFIX)%%build-version%bestselling_products_'
        ```
        **But be careful, don't add prefixes to the session client**

        And add a `global` client (it is also without the `build-version` prefix)
        ```diff
        snc_redis:
            clients:
        +       global:
        +           type: 'phpredis'
        +           alias: 'global'
        +           dsn: 'redis://%redis_host%'
        +           options:
        +               prefix: '%env(REDIS_PREFIX)%'
        ```
    - to `src/Shopsys/ShopBundle/Resources/config/services/commands.yml` add
        ```
        Shopsys\FrameworkBundle\Command\RedisCleanCacheOldCommand: ~
        ```
    - create `app/config/parameters_version.yml.dist` with following content
        ```yml
        parameters:
            build-version: %%version%%
        ```
    - to `build.xml` add new phing targets
        ```xml
        <target name="clean-redis-old" description="Cleans up redis cache for previous build versions">
            <exec executable="${path.php.executable}" passthru="true" checkreturn="true" output="${dev.null}">
                <arg value="${path.bin-console}" />
                <arg value="shopsys:redis:clean-cache-old" />
            </exec>
        </target>
        <target name="generate-build-version">
            <exec executable="${path.php.executable}" checkreturn="true" outputProperty="version">
                <arg value="-r" />
                <arg value="echo date('YmdHis');" />
            </exec>
            <copy file="${path.app}/config/parameters_version.yml.dist" tofile="${path.app}/config/parameters_version.yml" overwrite="true">
                <filterchain>
                    <replacetokens begintoken="%%" endtoken="%%">
                        <token key="version" value="${version}" />
                    </replacetokens>
                </filterchain>
            </copy>
        </target>
        ```
    - to `app/config/parameters_common.yml` add new parameter
        ```yml
        build-version: '0000000000000000'
        ```
    - download [`RedisVersionsFacadeTest.php`](https://github.com/shopsys/project-base/tree/v7.1.0/tests/ShopBundle/Functional/Component/Redis/RedisVersionsFacadeTest.php) to your tests directory `tests/ShopBundle/Functional/Component/Redis/`
    - run `php phing generate-build-version`
    - and include `generate-build-version` and `clean-redis-old` to your build phing targets. Please find inspiration in [#886](https://github.com/shopsys/shopsys/pull/886/files)
    - once you finish this change (include the `build-version` into caches), you still should deal with older redis cache keys that don't use `build-version` prefix (16 digits).
      Such keys are not removed even by `clean-redis-old`, please find and remove them manually (via console or UI)

## [shopsys/coding-standards]
- We disallow using [Doctrine inheritance mapping](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/inheritance-mapping.html) in the Shopsys Framework
  because it causes problems during entity extension. Such problem with `OrderItem` was resolved during [making OrderItem extendable #715](https://github.com/shopsys/shopsys/pull/715)  
  If you want to use Doctrine inheritance mapping anyway, please skip `Shopsys\CodingStandards\Sniffs\ForbiddenDoctrineInheritanceSniff` ([#848](https://github.com/shopsys/shopsys/pull/848))

[Upgrade from v7.0.0 to v7.1.0]: https://github.com/shopsys/shopsys/compare/v7.0.0...v7.1.0
[shopsys/framework]: https://github.com/shopsys/framework
[shopsys/coding-standards]: https://github.com/shopsys/coding-standards
