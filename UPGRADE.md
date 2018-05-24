# UPGRADING
There is a list of all the repositories maintained by monorepo, changes in log below are ordered as this list:

* [shopsys/framework]
* [shopsys/project-base]
* [shopsys/shopsys]
* [shopsys/coding-standards]
* [shopsys/form-types-bundle]
* [shopsys/http-smoke-testing]
* [shopsys/migrations]
* [shopsys/monorepo-tools]
* [shopsys/plugin-interface]
* [shopsys/product-feed-google]
* [shopsys/product-feed-heureka]
* [shopsys/product-feed-heureka-delivery]
* [shopsys/product-feed-interface]
* [shopsys/product-feed-zbozi]

## [From 7.0.0-alpha2 to Unreleased]

## [From 7.0.0-alpha1 to 7.0.0-alpha2]
### [shopsys/project-base]   
- check changes in the `docker-compose.yml` template you used, there were a couple of important changes you need to replicate 
    - easiest way is to overwrite your `docker-compose.yml` with by the appropriate template 
- on *nix systems, fill your UID and GID (you can run `id -u` and `id -g` to obtain them) into Docker build arguments `www_data_uid` and `www_data_gid` and rebuild your image via `docker-compose up --build` 
- change owner of the files in shared volume to `www-data` from the container by running `docker exec -u root shopsys-framework-php-fpm chown -R www-data /var/www/html` 
    - the user has shared UID, so you will be able to access it as well from the host machine 
    - shared volume with postgres data should be owned by `postgres` user: `docker exec -u root shopsys-framework-php-fpm chown -R postgres /var/www/html/var/postgres-data` 
- if you were using a mounted volume to share Composer cache with the container, change the target directory from `/root/.composer` to `/home/www-data/.composer` 
    - in such case, you should change the owner as well by running `docker exec -u root shopsys-framework-php-fpm chown -R www-data /home/www-data/.composer` 

## Before monorepo 
Before we managed to implement monorepo for our packages, we had slightly different versions for each of our package, 
that's why is this section formatted differently.  

### [shopsys/product-feed-heureka]
#### From 0.4.2 to 0.5.0
- requires possibility of extending the CRUD of categories via `shopsys.crud_extension` of type `category`
- requires update of [shopsys/plugin-interface](https://github.com/shopsys/plugin-interface) to version `^0.3.0`
and [shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface) to `^0.5.0`

#### From 0.4.0 to 0.4.1
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-030-to-040)

#### From 0.2.0 to 0.4.0
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-020-to-030)

#### From 0.1.0 to 0.2.0
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-010-to-020)

### [shopsys/product-feed-zbozi]
#### From 0.4.0 to 0.4.1
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-030-to-040)

#### From 0.3.0 to 0.4.0
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-020-to-030)

#### From 0.1.0 to 0.2.0
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-010-to-020)

### [shopsys/product-feed-heureka-delivery]
#### From 0.2.0 to 0.2.1
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-030-to-040)

#### From 0.1.1 to 0.2.0
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-010-to-030)

### [shopsys/product-feed-interface]
#### From 0.4.0 to 0.5.0
- implement method `getMainCategoryId()` in your implementations of `StandardFeedItemInterface`.

#### From 0.3.0 to 0.4.0
- implement method `isSellingDenied()` for all implementations of `StandardFeedItemInterface`.
- you have to take care of filtering of non-sellable items in implementations of `FeedConfigInterface::processItems()` 
in your product feed plugin because the instances of `StandardFeedItemInterface` passed as an argument can be non-sellable now.
- implement method `getAdditionalInformation()` in your implementations of `FeedConfigInterface`.
- implement method `getCurrencyCode()` in your implementations of `StandardFeedItemInterface`.

#### From 0.2.0 to 0.3.0
- remove method `getFeedItemRepository()` from all implementations and usages of `FeedConfigInterface`.

#### From 0.1.0 to 0.2.0
- Rename all implementations and usages of `FeedItemInterface::getItemId()` to `getId()`.
- Rename all implementations and usages of `FeedItemCustomValuesProviderInterface` to `HeurekaCategoryNameProviderInterface`.
- If you are using custom values in your implementation, you need to implement interfaces from package [shopsys/plugin-interface](https://github.com/shopsys/plugin-interface) (see [how to work with data storage interface](https://github.com/shopsys/plugin-interface#data-storage)).

### [shopsys/plugin-interface]
#### From 0.2.0 to 0.3.0
- all implementations of `DataStorageInterface` now must have implemented method `getAll()` for getting all saved data indexed by keys

### [shopsys/project-base]
#### From 2.0.0-beta.21.0 to 7.0.0-alpha1      
- manual upgrade from this version will be very hard because of BC-breaking extraction of [shopsys/framework](https://github.com/shopsys/framework)  
    - at this moment the core is not easily extensible by your individual functionality  
    - before upgrading to the new architecture you should upgrade to Dockerized architecture of `2.0.0-beta.21.0`  
    - the upgrade will require overriding or extending of all classes now located in  
    [shopsys/framework](https://github.com/shopsys/framework) that you customized in your forked repository  
    - it would be wise to wait with the upgrade until the newly build architecture has matured  
- update custom tests to be compatible with phpunit 7. For further details visit phpunit release announcements [phpunit 6](https://phpunit.de/announcements/phpunit-6.html) and [phpunit 7](https://phpunit.de/announcements/phpunit-7.html) 

#### From 2.0.0-beta.20.0 to 2.0.0-beta.21.0  
- do not longer use Phing targets standards-ci and standards-ci-diff, use standards and standards-diff instead 

#### From 2.0.0-beta.17.0 to 2.0.0-beta.18.0 
- use `SimpleCronModuleInterface` and `IteratedCronModuleInterface` from their new namespace `Shopsys\Plugin\Cron` (instead of `Shopsys\FrameworkBundle\Component\Cron`) 

#### From 2.0.0-beta.16.0 to 2.0.0-beta.17.0  
- coding standards for JS files were added, make sure `phing eslint-check` passes  
    (you can run `phing eslint-fix` to fix some violations automatically)  

#### From 2.0.0-beta.15.0 to 2.0.0-beta.16.0  
- all implementations of `Shopsys\ProductFeed\FeedItemRepositoryInterface` must implement interface `Shopsys\FrameworkBundle\Model\Feed\FeedItemRepositoryInterface` instead  
    - the interface was moved from [shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/) to core  
- parameter `email_for_error_reporting` was renamed to `error_reporting_email_to` in `app/config/parameter.yml.dist`,  
    you will be prompted to fill it out again during `composer install`  
- all implementations of `StandardFeedItemInterface` must implement methods `isSellingDenied()` and `getCurrencyCode()`, see [product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-030-to-040) 

### [shopsys/coding-standards]
#### From 3.x to 4.0
- In order to run all checks, there is new unified way - execute `php vendor/bin/ecs check /path/to/project --config=vendor/shopsys/coding-standards/easy-coding-standard.neon`
    - If you are overriding rules configuration in your project, it is necessary to do so in neon configuration file, see [example bellow](./example-of-custom-configuration-file).
    - See [EasyCodingStandard docs](https://github.com/Symplify/EasyCodingStandard#usage) for more information
##### Example of custom configuration file
###### Version 3.x and lower
```php
// custom phpcs-fixer.php_cs
<?php

$originalConfig = include __DIR__ . '/../vendor/shopsys/coding-standards/build/phpcs-fixer.php_cs';

$originalConfig->getFinder()
    ->exclude('_generated');

return $originalConfig;
```
###### Version 4.0 and higher
```neon
#custom-coding-standard.neon
includes:
    - vendor/symplify/easy-coding-standard/config/psr2-checkers.neon
    - vendor/shopsys/coding-standards/shopsys-coding-standard.neon
parameters:
    exclude_files:
        - *_generated/*

```

[From 7.0.0-alpha2 to Unreleased]: https://github.com/shopsys/shopsys/compare/v7.0.0-alpha2...HEAD
[From 7.0.0-alpha1 to 7.0.0-alpha2]: https://github.com/shopsys/shopsys/compare/v7.0.0-alpha1...v7.0.0-alpha2

[shopsys/shopsys]: https://github.com/shopsys/shopsys 
[shopsys/project-base]: https://github.com/shopsys/project-base 
[shopsys/framework]: https://github.com/shopsys/framework 
[shopsys/product-feed-zbozi]: https://github.com/shopsys/product-feed-zbozi 
[shopsys/product-feed-google]: https://github.com/shopsys/product-feed-google 
[shopsys/product-feed-heureka]: https://github.com/shopsys/product-feed-heureka 
[shopsys/product-feed-heureka-delivery]: https://github.com/shopsys/product-feed-heureka-delivery 
[shopsys/product-feed-interface]: https://github.com/shopsys/product-feed-interface 
[shopsys/plugin-interface]: https://github.com/shopsys/plugin-interface 
[shopsys/coding-standards]: https://github.com/shopsys/coding-standards 
[shopsys/http-smoke-testing]: https://github.com/shopsys/http-smoking-testing 
[shopsys/form-types-bundle]: https://github.com/shopsys/form-types-bundle 
[shopsys/migrations]: https://github.com/shopsys/migrations 
[shopsys/monorepo-tools]: https://github.com/shopsys/monorepo-tools
