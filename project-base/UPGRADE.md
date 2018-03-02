# Upgrading

## From 2.0.0-beta.21.0 to Unreleased
- manual upgrade from this version will be very hard because of BC-breaking extraction of [shopsys/framework](https://github.com/shopsys/framework)
    - at this moment the core is not easily extensible by your individual functionality
    - before upgrading to the new architecture you should upgrade to Dockerized architecture of `2.0.0-beta.21.0`
    - the upgrade will require overriding or extending of all classes now located in
    [shopsys/framework](https://github.com/shopsys/framework) that you customized in your forked repository
    - it would be wise to wait with the upgrade until the newly build architecture has matured

## From 2.0.0-beta.20.0 to 2.0.0-beta.21.0
- do not longer use Phing targets standards-ci and standards-ci-diff, use standards and standards-diff instead

## From 2.0.0-beta.17.0 to 2.0.0-beta.18.0
- use `SimpleCronModuleInterface` and `IteratedCronModuleInterface` from their new namespace `Shopsys\Plugin\Cron` (instead of `Shopsys\FrameworkBundle\Component\Cron`)

## From 2.0.0-beta.16.0 to 2.0.0-beta.17.0
- coding standards for JS files were added, make sure `phing eslint-check` passes
(you can run `phing eslint-fix` to fix some violations automatically)

## From 2.0.0-beta.15.0 to 2.0.0-beta.16.0
- all implementations of `Shopsys\ProductFeed\FeedItemRepositoryInterface` must implement interface `Shopsys\FrameworkBundle\Model\Feed\FeedItemRepositoryInterface` instead
    - the interface was moved from [shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/) to core
- parameter `email_for_error_reporting` was renamed to `error_reporting_email_to` in `app/config/parameter.yml.dist`,
you will be prompted to fill it out again during `composer install`
- all implementations of `StandardFeedItemInterface` must implement methods `isSellingDenied()` and `getCurrencyCode()`, see [product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-030-to-040)