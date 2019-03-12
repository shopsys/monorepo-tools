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

## [shopsys/coding-standards]
- We disallow using [Doctrine inheritance mapping](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/inheritance-mapping.html) in the Shopsys Framework
  because it causes problems during entity extension. Such problem with `OrderItem` was resolved during [making OrderItem extendable #715](https://github.com/shopsys/shopsys/pull/715)  
  If you want to use Doctrine inheritance mapping anyway, please skip `Shopsys\CodingStandards\Sniffs\ForbiddenDoctrineInheritanceSniff` ([#848](https://github.com/shopsys/shopsys/pull/848))

[Upgrade from v7.0.0 to Unreleased]: https://github.com/shopsys/shopsys/compare/v7.0.0...HEAD
[shopsys/framework]: https://github.com/shopsys/framework
[shopsys/coding-standards]: https://github.com/shopsys/coding-standards
