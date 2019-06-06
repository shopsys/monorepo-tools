# Upgrade Instructions for Upgrading PHPStan to Level 4

This article describes the upgrade instructions for [#1040 Upgrading PHPStan to level 4](https://github.com/shopsys/shopsys/pull/1040).
Upgrade instructions are in a separate article because there is a lot of instructions and we don't want to jam the [UPGRADE-unreleased.md](/docs/upgrade/UPGRADE-unreleased.md). <!--- TODO change to released version -->
Instructions are meant to be followed when you upgrade from `v7.2.0` to `unreleased`. <!--- TODO change to released version -->
We recommend upgrading to level 4 and increasing the level gradually from version 0 to 4 to ensure smooth implementation.

To upgrade level of PHPStan you need to:
- change the value of `phpstan.level` Phing property in your `build.xml` (`4` is the default value)
    ```xml
    <property name="phpstan.level" value="4"/>
    ```
    - if you have [overridden the `phpstan` Phing target](/docs/introduction/console-commands-for-application-management-phing-targets.md#customization-of-phing-targets-and-properties) or don't use the `build.xml` from `shopsys/framework` package yet, look for `<arg value="--level=0"/>` in your `build.xml` and change its value instead.
- add `phpstan-doctrine` and `phpstan-phpunit` extension packages as dev dependencies to `composer.json`
    ```diff
    "require-dev": {
        "phpstan/phpstan": "^0.11",
        "phpunit/phpunit": "^7.0",
        "shopsys/coding-standards": "dev-master",
        "shopsys/http-smoke-testing": "dev-master",
    +   "phpstan/phpstan-doctrine": "^0.11.2",
    +   "phpstan/phpstan-phpunit": "^0.11.2"
    ```
- include PHPStan extensions and add error skips to `phpstan.neon`
    ```diff
        parameters:
            ignoreErrors:
                # Add ignored errors here as regular expressions, e.g.:
                # - '#PHPUnit_Framework_MockObject_MockObject(.*) given#'
                - '#Undefined variable: \$undefined#'
    +           #ignore annotations in generated code#
    +           -
    +               message: '#(PHPDoc tag @(param|return) has invalid value .+ expected TOKEN_IDENTIFIER at offset \d+)#'
    +               path: %currentWorkingDirectory%/tests/ShopBundle/Test/Codeception/_generated/AcceptanceTesterActions.php
    +           -
    +               message: '#(PHPDoc tag @throws with type .+ is not subtype of Throwable)#'
    +               path: %currentWorkingDirectory%/tests/ShopBundle/Test/Codeception/_generated/AcceptanceTesterActions.php
    +           -
    +               message: '#Call to an undefined method Symfony\\Component\\Config\\Definition\\Builder\\NodeParentInterface::end\(\)#'
    +               path: %currentWorkingDirectory%/src/Shopsys/ShopBundle/DependencyInjection/Configuration.php
    +           - '#Method Doctrine\\Common\\Persistence\\ObjectManager::flush\(\) invoked with 1 parameter, 0 required\.#'
    +           -
    +               message: '#(Property Shopsys\\.+::\$.+ \(Shopsys\\.+\) does not accept object\.)#'
    +               path: %currentWorkingDirectory%/src/Shopsys/ShopBundle/DataFixtures/*
    +           -
    +               message: '#Method Shopsys\\ShopBundle\\DataFixtures\\ProductDataFixtureReferenceInjector::.+\(\) should return array<.+> but returns array<string, object>\.#'
    +               path: %currentWorkingDirectory%/src/Shopsys/ShopBundle/DataFixtures/ProductDataFixtureReferenceInjector.php
    +           -
    +               message: '#(Property (Shopsys|Tests)\\.+::\$.+ \(.+\) does not accept object\.)#'
    +               path: %currentWorkingDirectory%/tests/ShopBundle/*
    +           -
    +               message: '#(Method .+::.+\(\) should return .+ but returns (object|Codeception\\Module).)#'
    +               path: %currentWorkingDirectory%/tests/ShopBundle/*
    +           -
    +               message: '#Array \(array<.+>\) does not accept object\.#'
    +               path: %currentWorkingDirectory%/tests/ShopBundle/Functional/Model/Product/ProductOnCurrentDomainFacadeCountDataTest.php
    +   includes:
    +       - vendor/phpstan/phpstan-doctrine/extension.neon
    +       - vendor/phpstan/phpstan-phpunit/extension.neon
    ```
- after upgrading level of PHPStan you can expect that PHPStan will start reporting errors when running phing target `php phing phpstan`, you can get inspiration on how to fix them in [this commit.]()
- these errors can be caused by:
    - wrong annotation
        ```diff
        -   * @return string[]
        +   * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat[]  
        ```
    - missing annotation or abstract return argument (eg. PersistentReferenceFacade::getReference)
        ```diff
            $treeBuilder = new TreeBuilder();
        +   /** @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode */
            $rootNode = $treeBuilder->root('shopsys_shop');
        ```
        ```diff
        -   $categoryData->parent = $this->getReference(self::CATEGORY_ELECTRONICS);
        +   /** @var \Shopsys\ShopBundle\Model\Category\Category $categoryElectronics */
        +   $categoryElectronics = $this->getReference(self::CATEGORY_ELECTRONICS);
        +   $categoryData->parent = $categoryElectronics;
        ```
    - wrong value given to property
        ```diff
        -   $currencyData->exchangeRate = 25;
        +   $currencyData->exchangeRate = '25';

        -   $orderData->companyTaxNumber = $this->faker->randomNumber(6);
        +   $orderData->companyTaxNumber = (string)$this->faker->randomNumber(6);
        ```
    - variables that can be undefined
        ```diff
        +   $manualPricesColumn = '';
            if ($domainId === 1) {
                $manualPricesColumn = $row[self::COLUMN_MANUAL_PRICES_DOMAIN_1];
            } elseif ($domainId === 2) {
                $manualPricesColumn = $row[self::COLUMN_MANUAL_PRICES_DOMAIN_2];
            }
        ```
- change usage of `select` to `setValue` in `tests/ShopBundle/Smoke/NewProductTest.php`
    ```diff
    -   $form['product_form[pricesGroup][vat]']->select($vat->getId());
    +   $form['product_form[pricesGroup][vat]']->setValue($vat->getId());

    //...

    -   $form['product_form[displayAvailabilityGroup][unit]']->select($unit->getId());
    -   $form['product_form[displayAvailabilityGroup][availability]']->select($availability->getId());
    +   $form['product_form[displayAvailabilityGroup][unit]']->setValue($unit->getId());
    +   $form['product_form[displayAvailabilityGroup][availability]']->setValue($availability->getId());
    ```
- fix typo in `OrderDataFixture::getRandomCountryFromFirstDomain()`
    ```diff
    -   $randomPaymentReferenceName = $this->faker->randomElement([
    +   $randomCountryReferenceName = $this->faker->randomElement([
    ```