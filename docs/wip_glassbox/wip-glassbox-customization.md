# WIP Glassbox Customization

## Migrations
* migrations now can be installed from all bundles registered in application, directory should be in bundle_root/Migrations folder

## Forms
State of forms is described much more in this [document](form-extension.md)

## Entities
* visibility of all private properties and methods of repositories of entities was changed to protected (@Miroslav-Stopka)
    * there are changed only repositories of entities because currently there was no need for extendibility of other repositories
    * protected visibility allows overriding of behavior from projects
* visibility of all private properties and methods of DataFactories was changed to protected (@Miroslav-Stopka)
    * protected visibility allows overriding of behavior from projects
* entities can be extended by inheritance
* all entities are extensible via `%shopsys.entity_extension.map%` parameter
* more info in separate article [Entity Extension](entity-extension.md)
* reasons and alternatives to this approach are explained in [Entity Extension vs. Entity Generation](entity-extension-vs-entity-generation.md)

## Product Feeds
* creation of a new custom feed is described in [Product Feeds](../introduction/product-feeds.md#how-to-implement-a-custom-product-feed) article
* [already existing product feed modules](https://github.com/search?q=topic%3Aproduct-feed+org%3Ashopsys) can be customized in several ways:
    * to use a different Twig template you can either [override the template](https://symfony.com/doc/3.3/templating/overriding.html)
    or you can extend the service tagged as `shopsys.product_feed` and override the `getTemplateFilepath` method in it
    * you can use a different `FeedItemInterface` implementation by extending its factory service
    (eg. [GoogleFeedItemFactory](../../packages/product-feed-google/src/Model/FeedItem/GoogleFeedItemFactory.php))
    * you can even change the way the underlying Product entities are fetched from the database by extending the feed's product repository
    (eg. [GoogleProductRepository](../../packages/product-feed-google/src/Model/Product/GoogleProductRepository.php))
    * when a more complicated customization is needed, extending feed item facade service and overwriting the `getItems` is the way to go
    (eg. [GoogleFeedItemFacade](../../packages/product-feed-google/src/Model/FeedItem/GoogleFeedItemFacade.php)),
    it should allow you to provide your own way of getting the right items for your feed
