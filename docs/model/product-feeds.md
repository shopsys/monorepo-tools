# Product Feeds

Product feeds are a way to periodically export information about your products for product search engines such as [Google Shopping](https://www.google.com/shopping).

In order to allow easy installation and removal of product feeds, they are implemented in form of plugins ([see list of current implementations](https://github.com/search?q=topic%3Aproduct-feed+org%3Ashopsys)).

## Where are the feeds?

The exported files contain a random token generated during the application build, so their URL address is not easily guessed.
You can see all installed product feeds along with the URLs of their export in the administration section *Marketing >  XML Feeds*.

## When are they exported?

Product feeds are usually exported using Cron modules.
The Cron modules are already implemented and registered, all that's needed is to run the [`cron` phing target](../introduction/console-commands-for-application-management-phing-targets.md#cron) every 5 minutes on your server and Shopsys Framework takes care of the rest.
They can be also generated manually in the administration section *Marketing >  XML Feeds*, if you're logged in as *superadministrator*.

There are two types of product feeds: `daily` and `hourly`.

Daily feed usually contain a lot of information about the products are can take a while to export if you have a lot of products.
For this exact reason, the [`DailyFeedCronModule`](../../packages/framework/src/Model/Feed/DailyFeedCronModule.php) is implemented iteratively and can export the feeds in batches as needed.

Hourly feeds contain much less information and their priority is to be as current as possible.
Typical examples are exports of current availability and stock quantities of the products.
By default, they are exported every hour and the export cannot be broken into multiple Cron executions.

## How to implement a custom product feed?

The heart of a product feed plugin is a service implementing the [`FeedInterface`](../../packages/framework/src/Model/Feed/FeedInterface.php) that is tagged in a DI container with `shopsys.product_feed` tag.
Optionally, the tag can have a type attribute (default is `daily`).

The annotations in the feed interfaces ([`FeedInterface`](../../packages/framework/src/Model/Feed/FeedInterface.php), [`FeedInfoInterface`](../../packages/framework/src/Model/Feed/FeedInfoInterface.php) and [`FeedItemInterface`](../../packages/framework/src/Model/Feed/FeedItemInterface.php)) should explain a lot.
When in doubt, you can take a look at the [already implemented product feeds](https://github.com/search?q=topic%3Aproduct-feed+org%3Ashopsys) for inspiration.

## How to extend an existing product feed?

[Already existing product feed modules](https://github.com/search?q=topic%3Aproduct-feed+org%3Ashopsys) can be customized in several ways:
* to use a different Twig template you can either [override the template](https://symfony.com/doc/3.3/templating/overriding.html)
or you can extend the service tagged as `shopsys.product_feed` and override the `getTemplateFilepath` method in it
* you can use a different `FeedItemInterface` implementation by extending its factory service
(eg. [GoogleFeedItemFactory](../../packages/product-feed-google/src/Model/FeedItem/GoogleFeedItemFactory.php))
* you can even change the way the underlying Product entities are fetched from the database by extending the feed's product repository
(eg. [GoogleProductRepository](../../packages/product-feed-google/src/Model/Product/GoogleProductRepository.php))
* when a more complicated customization is needed, extending feed item facade service and overwriting the `getItems` is the way to go
(eg. [GoogleFeedItemFacade](../../packages/product-feed-google/src/Model/FeedItem/GoogleFeedItemFacade.php)),
it should allow you to provide your own way of getting the right items for your feed

