# Upgrading

## From 2.0.0-beta.20.0 to Unreleased

## From 2.0.0-beta.17.0 to 2.0.0-beta.18.0
- use `SimpleCronModuleInterface` and `IteratedCronModuleInterface` from their new namespace `Shopsys\Plugin\Cron` (instead of `Shopsys\ShopBundle\Component\Cron`)

## From 2.0.0-beta.16.0 to 2.0.0-beta.17.0
- coding standards for JS files were added, make sure `phing eslint-check` passes
(you can run `phing eslint-fix` to fix some violations automatically)

## From 2.0.0-beta.15.0 to 2.0.0-beta.16.0
- all implementations of `Shopsys\ProductFeed\FeedItemRepositoryInterface` must implement interface `Shopsys\ShopBundle\Model\Feed\FeedItemRepositoryInterface` instead
    - the interface was moved from [shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/) to core
- parameter `email_for_error_reporting` was renamed to `error_reporting_email_to` in `app/config/parameter.yml.dist`,
you will be prompted to fill it out again during `composer install`
- all implementations of `StandardFeedItemInterface` must implement methods `isSellingDenied()` and `getCurrencyCode()`, see [product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-030-to-040)