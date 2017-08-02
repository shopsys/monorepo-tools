# General Shopsys Framework Plugin Interface
Package of interfaces providing compatibility between [Shopsys Framework](https://www.shopsys-framework.com) and plugins.

This package contains interfaces responsible for general functionality usable in almost any plugin.
For specific functionality, such as generating product feeds, there are [separate repositories](https://github.com/search?q=topic%3Aplugin-interface+org%3Ashopsys&type=Repositories), eg. [ProductFeedInterface](https://github.com/shopsys/product-feed-interface/).

## How to implement a plugin
Plugins are implemented in a form of a [Symfony bundle](http://symfony.com/doc/current/bundles.html).
For tips on how to write a new bundle see [Best Practices for Reusable Bundles](https://symfony.com/doc/current/bundles/best_practices.html).
