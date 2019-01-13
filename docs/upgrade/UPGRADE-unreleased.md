# [Upgrade from v7.0.0-beta5 to Unreleased]

This guide contains instructions to upgrade from version v7.0.0-beta5 to Unreleased.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]
### Infrastructure
- *(optional)* in your `docker/php-fpm/Dockerfile` change base image to `php:7.3-fpm-stretch` ([#694](https://github.com/shopsys/shopsys/pull/694))

### Tools
- *(optional)* add a new phing target `clean-redis` to your `build.xml` and `build-dev.xml` and use it where you need to clean Redis cache.
  You can find an inspiration in [#736](https://github.com/shopsys/shopsys/pull/736/files)
    ```xml
        <target name="clean-redis" description="Cleans up redis cache">
            <exec executable="${path.php.executable}" passthru="true" checkreturn="true" output="${dev.null}">
                <arg value="${path.bin-console}" />
                <arg value="shopsys:redis:clean-cache" />
            </exec>
        </target>
    ```

### Application
- if you were using `oneup/flysystembundle` for using different adapter than the local one, you must now implement `FilesystemFactoryInterface` and init the adapter by yourself.
- *(optional)* delete dependency on `oneup/flysystembundle` from your `composer.json`
- remove usages of inherited `OrderItem` classes ([#715](https://github.com/shopsys/shopsys/pull/715))
    - replace usages of `OrderProduct`, `OrderPayment`, and `OrderTransport` with common `OrderItem`
        - use `isType<type>()` method instead of `instanceof`
    - replace usages of `OrderTransportData`, `OrderPaymentData` with common `OrderItemData`
    - replace usages of `OrderProductFactoryInterface`, `OrderPaymentFactoryInterface` and `OrderTransportFactoryInterface` with common `OrderItemFactoryInterface`
        - replace usages of `OrderProductFactory`, `OrderPaymentFactory` and `OrderTransportFactory` with `OrderItemFactory`
        - replace usages of method `create()` with `createProduct()`, `createPayment()` or `createTransport()`, respectively
    - replace usages of `OrderPaymentDataFactoryInterface` and `OrderTransportDataFactoryInterface` with common `OrderItemDataFactoryInterface`
        - replace usages of `OrderPaymentDataFactory` and `OrderTransportDataFactory` with common `OrderItemDataFactory`
        - replaces usages of method `createFromOrderPayment()` and `createFromOrderTransport()` with `createFromOrderItem()`
    - following classes changed constructors, if you extend them, change them appropriately:
        - `Order`
        - `OrderDataFactory`
        - `OrderItemFacade`
        - `OrderFacade`
    - remove non-existing test cases from `EntityExtensionTest`
        - remove `ExtendedOrder*` classes
        - remove calling `doTestExtendedEntityInstantiation` with classes that are removed
        - change `ExtendedOrderItem` to standard class - remove `abstract` and inheritance annotations
        - change `doTestExtendedOrderItemsPersistence` to test only `OrderItem`
        - please find inspiration in [#715](https://github.com/shopsys/shopsys/pull/715/files)

[Upgrade from v7.0.0-beta5 to Unreleased]: https://github.com/shopsys/shopsys/compare/v7.0.0-beta5...HEAD
[shopsys/shopsys]: https://github.com/shopsys/shopsys
[shopsys/project-base]: https://github.com/shopsys/project-base
[shopsys/framework]: https://github.com/shopsys/framework
[shopsys/product-feed-zbozi]: https://github.com/shopsys/product-feed-zbozi
[shopsys/product-feed-google]: https://github.com/shopsys/product-feed-google
[shopsys/product-feed-heureka]: https://github.com/shopsys/product-feed-heureka
[shopsys/product-feed-heureka-delivery]: https://github.com/shopsys/product-feed-heureka-delivery
[shopsys/product-feed-interface]: https://github.com/shopsys/product-feed-interface
[shopsys/plugin-interface]: https://github.com/shopsys/plugin-interface
[shopsys/coding-standards]: https://github.com/shopsys/coding-standards
[shopsys/http-smoke-testing]: https://github.com/shopsys/http-smoke-testing
[shopsys/form-types-bundle]: https://github.com/shopsys/form-types-bundle
[shopsys/migrations]: https://github.com/shopsys/migrations
[shopsys/monorepo-tools]: https://github.com/shopsys/monorepo-tools
[shopsys/microservice-product-search]: https://github.com/shopsys/microservice-product-search
[shopsys/microservice-product-search-export]: https://github.com/shopsys/microservice-product-search-export
