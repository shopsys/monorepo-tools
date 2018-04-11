# Shopsys Product Feed Interface

[![Build Status](https://travis-ci.org/shopsys/product-feed-interface.svg?branch=master)](https://travis-ci.org/shopsys/product-feed-interface)
[![Downloads](https://img.shields.io/packagist/dt/shopsys/product-feed-interface.svg)](https://packagist.org/packages/shopsys/product-feed-interface)

Package of interfaces providing compatibility between [Shopsys Framework](https://www.shopsys-framework.com) and product feed plugins. 

## How to implement a plugin
Product feed plugins are implemented in a form of a [Symfony bundle](http://symfony.com/doc/current/bundles.html).
For tips on how to write a new bundle see [Best Practices for Reusable Bundles](https://symfony.com/doc/current/bundles/best_practices.html).

The heart of a product feed plugin is a service implementing [FeedConfigInterface](./src/FeedConfigInterface.php)
that is [tagged in a DI container](http://symfony.com/doc/current/service_container/tags.html) with `shopsys.product_feed` tag.

Optionally, the tag can have a `type` attribute:

### `standard`
- if the tag `type` is omitted, the `standard` is used as a default value
- feed usually contains most of the product parameters and information (eg. parameters, description, ean)
- items passed to FeedConfigInterface::processItems() are instances of [StandardFeedItemInterface](./src/StandardFeedItemInterface.php)

### `delivery`
- feed usually contains only information about stock quantity or time of delivery
- items for feed generation are instances of [DeliveryFeedItemInterface](./src/DeliveryFeedItemInterface.php)
- used for feeds that need a frequent generation

The methods to be implemented are described in the [FeedConfigInterface](./src/FeedConfigInterface.php) itself.

For general information on plugin creation see the documentation of the [General Shopsys Framework Plugin Interface repository](https://github.com/shopsys/plugin-interface). 

### Example
You can take a look at the package [shopsys/product-feed-zbozi](https://github.com/shopsys/product-feed-zbozi/)
for implementation of [Zboží.cz](https://www.zbozi.cz) product feed plugin.

## Contributing
Thank you for your contributions to Shopsys Product Feed Interface package.
Together we are making Shopsys Framework better.

This repository is READ-ONLY.
If you want to [report issues](https://github.com/shopsys/shopsys/issues/new) and/or send [pull requests](https://github.com/shopsys/shopsys/compare),
please use the main [Shopsys repository](https://github.com/shopsys/shopsys).

Please, check our [Contribution Guide](https://github.com/shopsys/shopsys/CONTRIBUTING.md) before contributing.

## Need help
Contact us on our Slack [http://slack.shopsys-framework.com/](http://slack.shopsys-framework.com/).
