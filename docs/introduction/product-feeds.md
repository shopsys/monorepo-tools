# Product Feeds

Product feeds are a way to periodically export information about your products for product search engines such as [Google Shopping](https://www.google.com/shopping).

In order to allow easy installation and removal of product feeds, they are implemented in form of plugins ([see list of current implementations](https://github.com/search?q=topic%3Aproduct-feed+org%3Ashopsys)).

## Where are the feeds?

The exported files contain a random token generated during the application build, so their URL address is not easily guessed.
You can see all installed product feeds along with the URLs of their export in the administration section *Marketing >  XML Feeds*.

## When are they exported?

Product feeds are usually exported using Cron modules.
The Cron modules are already implemented and registered, all that's needed is to run the [`cron` phing target](phing-targets.md#cron) every 5 minutes on your server and Shopsys Framework takes care of the rest.
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
