# Upgrading
## From 2.0.0-beta.15.0 to Unreleased
- all implementations of `Shopsys\ProductFeed\FeedItemRepositoryInterface` must implement interface `Shopsys\ShopBundle\Model\Feed\FeedItemRepositoryInterface` instead
    - the interface was moved from [shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/) to core
- parameter `email_for_error_reporting` was renamed to `error_reporting_email_to` in `app/config/parameter.yml.dist`,
you will be prompted to fill it out again during `composer install`