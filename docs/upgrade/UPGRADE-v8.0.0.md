# Upgrade to v8.0.0

This guide contains instructions to upgrade to v8.0.0.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]

### Application
- constructors of `FrameworkBundle\Model\Mail\Mailer` and `FrameworkBundle\Component\Cron\CronFacade` classes were changed so if you extend them change them accordingly: ([#875](https://github.com/shopsys/shopsys/pull/875)).
    - `CronFacade::__construct(Logger $logger, CronConfig $cronConfig, CronModuleFacade $cronModuleFacade, Mailer $mailer)`
    - `Mailer::__construct(Swift_Mailer $swiftMailer, Swift_Transport $realSwiftTransport)`
    - find all usages of the constructors and fix them
- `EntityNameResolver` was added into constructor of these classes: ([#918](https://github.com/shopsys/shopsys/pull/918))
    - CronModuleFactory
    - PersistentReferenceFactory
    - ImageFactory
    - FriendlyUrlFactory
    - SettingValueFactory
    - UploadedFileFactory
    - AdministratorGridLimitFactory
    - EnabledModuleFactory
    - ProductCategoryDomainFactory
    - ProductVisibilityFactory
    - ScriptFactory
    - SliderItemFactory

    In case of extending one of these classes, you should add an `EntityNameResolver` to a constructor and use it in a `create()` method to resolve correct class to return.
- run `php phing standards-fix` so all nullable values will be now defined using nullability (?) symbol ([#1010](https://github.com/shopsys/shopsys/pull/1010))
- replace `IvoryCKEditorBundle` with `FOSCKEditorBundle` ([#1072](https://github.com/shopsys/shopsys/pull/1072))
    - replace the registration of the bundle in `app/AppKernel`
        ```diff
        - new Ivory\CKEditorBundle\IvoryCKEditorBundle(), // has to be loaded after FrameworkBundle and TwigBundle
        + new FOS\CKEditorBundle\FOSCKEditorBundle(), // has to be loaded after FrameworkBundle and TwigBundle
        ```
    - rename `app/config/packages/ivory_ck_editor.yml` to `app/config/packages/fos_ck_editor.yml` and change the root key in its content
        ```diff
        - ivory_ck_editor:
        + fos_ck_editor:
        ```
    - change the package in `composer.json`
        ```diff
        - "egeloen/ckeditor-bundle": "^4.0.6",
        + "friendsofsymfony/ckeditor-bundle": "^2.1",
        ```
    - update all usages of the old bundle in
        - extended twig templates like
            ```diff
            - {% use '@IvoryCKEditor/Form/ckeditor_widget.html.twig' with ckeditor_widget as base_ckeditor_widget %}
            + {% use '@FOSCKEditor/Form/ckeditor_widget.html.twig' with ckeditor_widget as base_ckeditor_widget %}
            ```
        - javascripts like
            ```diff
            - if (element.type === Shopsys.constant('\\Ivory\\CKEditorBundle\\Form\\Type\\CKEditorType::class')) {
            + if (element.type === Shopsys.constant('\\FOS\\CKEditorBundle\\Form\\Type\\CKEditorType::class')) {
            ```
        - configuration files like
            ```diff
            Shopsys\FrameworkBundle\Form\WysiwygTypeExtension:
                tags:
            -       - { name: form.type_extension, extended_type: Ivory\CKEditorBundle\Form\Type\CKEditorType }
            +       - { name: form.type_extension, extended_type: FOS\CKEditorBundle\Form\Type\CKEditorType }
            ```
        - php code like
            ```diff
            namespace Shopsys\FrameworkBundle\Form\Admin\Transport;

            - use Ivory\CKEditorBundle\Form\Type\CKEditorType;
            + use FOS\CKEditorBundle\Form\Type\CKEditorType;
            ```
- update the minimal PHP version in your `composer.json` in `require` and `config.platform` section to `7.2` because version `7.1` is no longer supported in Shopsys Framework ([#1066](https://github.com/shopsys/shopsys/pull/1066))
- if you want to use our experimental API follow these instructions in [the separate article](upgrade-instructions-for-backend-api.md) to introduce backend API into your project ([#1055](https://github.com/shopsys/shopsys/pull/1055))
    - we recommend to read [introduction to backend API](/docs/backend-api/introduction-to-backend-api.md) article as well
- run [db-create](/docs/introduction/console-commands-for-application-management-phing-targets.md#db-create) (this one even on production) and `test-db-create` phing targets to install extension for UUID
- update your application and tests to correctly handle availabilities and stock ([#1115](https://github.com/shopsys/shopsys/pull/1115))
    - copy and replace the functional test [AvailabilityFacadeTest.php](https://github.com/shopsys/project-base/blob/master/tests/ShopBundle/Functional/Model/Product/Availability/AvailabilityFacadeTest.php) in `tests/ShopBundle/Functional/Model/Product/Availability/` to test deletion and replacement of availabilities properly
    - if you have made any custom changes to the test you should merge your changes with the ones described in the pull request linked above
    - add a test service definition for `AvailabilityDataFactory` in your `src/Shopsys/ShopBundle/Resources/config/services_test.yml` configuration:
        ```diff
            Shopsys\FrameworkBundle\Model\Transport\TransportDataFactoryInterface: '@Shopsys\ShopBundle\Model\Transport\TransportDataFactory'

        +   Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityDataFactoryInterface: '@Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityDataFactory'
        +
            Shopsys\FrameworkBundle\Model\Payment\PaymentDataFactoryInterface: '@Shopsys\ShopBundle\Model\Payment\PaymentDataFactory'
        ```
    - check and fix your other tests, they might start failing if they assumed `Product::$availability` is not null when the product is using stock, or that stock quantity is not null when it's not using stock
- follow upgrade instructions for entities simplification in the [separate article](./upgrade-instructions-for-entities-simplification.md) ([#1123](https://github.com/shopsys/shopsys/pull/1123))
- JS functionality connected to `#js-close-without-saving` has been removed, implement your own if you relied on this ([#1168](https://github.com/shopsys/shopsys/pull/1168))
- update your way of registration of `FriendlyUrlDataProviders` ([#1140](https://github.com/shopsys/shopsys/pull/1140))
    - the namespace of `FriendlyUrlDataProviderInterface` and `FriendlyUrlDataProviderRegistry` has changed from `Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\CompilerPass` to `Shopsys\FrameworkBundle\Component\Router\FriendlyUrl` so change all your usages accordingly
    - you no longer need to tag your `FriendlyUrlDataProviders` with `shopsys.friendly_url_provider` because it is now done automatically
    - remove the usages of `RegisterFriendlyUrlDataProviderCompilerPass` class and `FriendlyUrlDataProviderRegistry::registerFriendlyUrlDataProvider` method because they have been removed
- update your way of registration of `BreadcrumbGenerator` classes ([#1141](https://github.com/shopsys/shopsys/pull/1140))
    - remove the usages of `FrontBreadcrumbResolverFactory` class as it has been removed.
    - remove the usages of `BreadcrumbResolver::registerGenerator` method as it has been removed
    - update your usages of `BreadcrumbResolver::__contruct()` as it now requires a new parameter

### Configuration
- simplify local configuration ([#1004](https://github.com/shopsys/shopsys/pull/1004))
    - update `app/config/packages/shopsys_shop.yml`
        ```diff
        router:
        -   locale_router_filepaths:
        -       cs: '%shopsys.root_dir%/src/Shopsys/ShopBundle/Resources/config/routing_front_cs.yml'
        -       en: '%shopsys.root_dir%/src/Shopsys/ShopBundle/Resources/config/routing_front_en.yml'
        +   locale_router_filepath_mask: '%shopsys.root_dir%/src/Shopsys/ShopBundle/Resources/config/routing_front_*.yml'
        ```
    - update `app/config/packages/shopsys_shop.yml`
        ```diff
        ->arrayNode('router')
            ->children()
        -       ->arrayNode('locale_router_filepaths')
        -           ->defaultValue([])
        -           ->prototype('scalar')
        +       ->scalarNode('locale_router_filepath_mask')
        +       ->end()
        +       ->scalarNode('friendly_url_router_filepath')
                ->end()
        -   ->end()
        -   ->scalarNode('friendly_url_router_filepath')
            ->end()
        ```
    - update `src/Shopsys/ShopBundle/DependencyInjection/ShopsysShopExtension.php`
        ```diff
        - $container->setParameter('shopsys.router.locale_router_filepaths', $config['router']['locale_router_filepaths']);
        + $container->setParameter('shopsys.router.locale_router_filepath_mask', $config['router']['locale_router_filepath_mask']);
        ```

[shopsys/framework]: https://github.com/shopsys/framework
