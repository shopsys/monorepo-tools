# Upgrade Instructions for Upgrading PHPStan to Level 1

This article describes the upgrade instructions for [#1040 Upgrading PHPStan](https://github.com/shopsys/shopsys/pull/1040).
Pull request updates PHPStan in framework to level 4, but recommended level for your project is 1 as level 4 does not comply with framework extensibility options.

Upgrade instructions are in a separate article because there is a lot of instructions and we don't want to jam the [UPGRADE-v7.3.0](/docs/upgrade/UPGRADE-v7.3.0.md).
Instructions are meant to be followed when you upgrade from `v7.2.0` to `v7.3.0`.

To upgrade level of PHPStan you need to:
- change the value of `phpstan.level` Phing property in your `build.xml`
    ```xml
    <property name="phpstan.level" value="1"/>
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
- include PHPStan extensions in `phpstan.neon`
    ```diff
        parameters:
            ignoreErrors:
                # Add ignored errors here as regular expressions, e.g.:
                # - '#PHPUnit_Framework_MockObject_MockObject(.*) given#'
                - '#Undefined variable: \$undefined#'
    +   includes:
    +       - vendor/phpstan/phpstan-doctrine/extension.neon
    +       - vendor/phpstan/phpstan-phpunit/extension.neon
    ```
- after upgrading level of PHPStan you can expect that PHPStan will start reporting errors when running phing target `php phing phpstan`
- these errors can be caused by:
    - variables that can be undefined
        ```diff
        +   $manualPricesColumn = '';
            if ($domainId === 1) {
                $manualPricesColumn = $row[self::COLUMN_MANUAL_PRICES_DOMAIN_1];
            } elseif ($domainId === 2) {
                $manualPricesColumn = $row[self::COLUMN_MANUAL_PRICES_DOMAIN_2];
            }
        ```
    - not existing constants
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
