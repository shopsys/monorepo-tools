# General Shopsys Framework Plugin Interface
Package of interfaces providing compatibility between [Shopsys Framework](https://www.shopsys-framework.com) and plugins.

## Features
This package contains interfaces responsible for general functionality usable in almost any plugin.
For specific functionality, such as generating product feeds, there are [separate repositories](https://github.com/search?q=topic%3Aplugin-interface+org%3Ashopsys&type=Repositories), eg. [ProductFeedInterface](https://github.com/shopsys/product-feed-interface/).

### Data storage
A lot of plugins need to persist some kind of custom data, for example, the last time a command was executed.

For this task you can use [`DataStorageInterface`](./src/DataStorageInterface.php) that has all the methods you need.
You can safely persist scalar values or arrays in a key-value storage fashion.

To get the instance of the data storage you can call `getDataStorage()` method on a service from Shopsys Framework implementing [`PluginDataStorageProviderInterface`](./src/PluginDataStorageProviderInterface.php).

See [`\Shopsys\Plugin\DataStorageInterface`](./src/DataStorageInterface.php) and [`\Shopsys\Plugin\PluginDataStorageProviderInterface`](./src/PluginDataStorageProviderInterface.php) for details.

## How to implement a plugin
Plugins are implemented in a form of a [Symfony bundle](http://symfony.com/doc/current/bundles.html).
For tips on how to write a new bundle see [Best Practices for Reusable Bundles](https://symfony.com/doc/current/bundles/best_practices.html).
