# Shopsys Framework Product Feed Plugin Interface
Package of interfaces providing compatibility between [Shopsys Framework](https://www.shopsys-framework.com) and product feed plugins. 

## How to implement a plugin
Product feed plugins are implemented in a form of a [Symfony bundle](http://symfony.com/doc/current/bundles.html).
For tips on how to write a new bundle see [Best Practices for Reusable Bundles](https://symfony.com/doc/current/bundles/best_practices.html).

The heart of a product feed plugin is a service implementing [FeedConfigInterface](./src/FeedConfigInterface.php)
that is [tagged in a DI container](http://symfony.com/doc/current/service_container/tags.html) with `shopsys.product_feed` tag.

Optionally, the tag can have a `type` attribute:
* `default` for standard product feeds
* `delivery` for feeds that need a frequent generation

The methods to be implemented are described in the [FeedConfigInterface](./src/FeedConfigInterface.php) itself.

For general information on plugin creation see the documentation of the [General Shopsys Framework Plugin Interface repository](https://github.com/shopsys/plugin-interface). 

### Example
You can take a look at the package [shopsys/product-feed-zbozi](https://github.com/shopsys/product-feed-zbozi/)
for implementation of [Zboží.cz](https://www.zbozi.cz) product feed plugin.
