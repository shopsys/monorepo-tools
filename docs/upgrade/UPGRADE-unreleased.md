# [Upgrade from v7.0.0 to Unreleased]

This guide contains instructions to upgrade from version v7.0.0 to Unreleased.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]
### Application
- *(low priority)* to add support of functional tests of Redis ([#846](https://github.com/shopsys/shopsys/pull/846))
    - download [`snc_redis.yml`](https://github.com/shopsys/project-base/blob/master/app/config/packages/test/snc_redis.yml) to `app/config/packages/test/snc_redis.yml`
    - download [`RedisFacadeTest.php`](https://github.com/shopsys/project-base/tree/master/tests/ShopBundle/Functional/Component/Redis/RedisFacadeTest.php) to your tests directory `tests/ShopBundle/Functional/Component/Redis/`
- *(low-priority)* by changes realized in [#808 redesign print page of product detail page](https://github.com/shopsys/shopsys/pull/808) was created new folder `print` in `src/Shopsys/ShopBundle/Resources/styles/front/common`. In the pull request were realized changes for styling print page of initial Shopsys Framework configuration. In order to apply changes in your project there is need to do following steps.
    - copy whole `print` folders from `src/Shopsys/ShopBundle/Resources/styles/front/common/print` and `src/Shopsys/ShopBundle/Resources/styles/front/domain2/print` of [shopsys/project-base](https://github.com/shopsys/project-base/)
    - add `dont-print` class to HTML elements which you want to hide. You can inspire by [changes](https://github.com/shopsys/shopsys/pull/808/files) realized in [pull request](https://github.com/shopsys/shopsys/pull/808).
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
    - download [`RedisVersionsFacadeTest.php`](https://github.com/shopsys/project-base/tree/master/tests/ShopBundle/Functional/Component/Redis/RedisVersionsFacadeTest.php) to your tests directory `tests/ShopBundle/Functional/Component/Redis/`
    - run `php phing generate-build-version`
    - and include `generate-build-version` and `clean-redis-old` to your build phing targets. Please find inspiration in [#886](https://github.com/shopsys/shopsys/pull/886/files)
    - once you finish this change (include the `build-version` into caches), you still should deal with older redis cache keys that don't use `build-version` prefix (16 digits).
      Such keys are not removed even by `clean-redis-old`, please find and remove them manually (via console or UI)

## [shopsys/coding-standards]
- We disallow using [Doctrine inheritance mapping](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/inheritance-mapping.html) in the Shopsys Framework
  because it causes problems during entity extension. Such problem with `OrderItem` was resolved during [making OrderItem extendable #715](https://github.com/shopsys/shopsys/pull/715)  
  If you want to use Doctrine inheritance mapping anyway, please skip `Shopsys\CodingStandards\Sniffs\ForbiddenDoctrineInheritanceSniff` ([#848](https://github.com/shopsys/shopsys/pull/848))

[Upgrade from v7.0.0 to Unreleased]: https://github.com/shopsys/shopsys/compare/v7.0.0...HEAD
[shopsys/framework]: https://github.com/shopsys/framework
[shopsys/coding-standards]: https://github.com/shopsys/coding-standards
