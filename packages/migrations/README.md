## About

[![Build Status](https://travis-ci.org/shopsys/migrations.svg?branch=master)](https://travis-ci.org/shopsys/migrations)
[![Downloads](https://img.shields.io/packagist/dt/shopsys/migrations.svg)](https://packagist.org/packages/shopsys/migrations)

This bundle provides commands which can be useful for database migrations:
* `shopsys:migrations:check-mapping` checks if ORM mapping is valid.
* `shopsys:migrations:check-schema` checks if database schema is satisfying ORM.
* `shopsys:migrations:count` returns count of migrations to execute.
* `shopsys:migrations:migrate` executes all database migrations in one transaction.
* `shopsys:migrations:generate` generates database migrations if necessary
    * the command does not generate migrations that break backwards compatibility - eg. column dropping
    * prompts you to choose the location for migration file if you are developing more than one bundle

This bundle uses [DoctrineMigrationsBundle](https://symfony.com/doc/current/bundles/DoctrineMigrationsBundle), so you have to install both.

## To be able to use this bundle, you need to do following:
1. Require `shopsys/migrations` in `composer.json`
2. Register bundles in your `AppKernel.php`:

    ```
    new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
    new Shopsys\MigrationBundle\ShopsysMigrationBundle(),
    ```
3. Configure `DoctrineMigrationsBundle` according to its documentation (see https://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html#configuration)

## Contributing
Thank you for your contributions to Shopsys Migrations package.
Together we are making Shopsys Framework better.

This repository is READ-ONLY.
If you want to [report issues](https://github.com/shopsys/shopsys/issues/new) and/or send [pull requests](https://github.com/shopsys/shopsys/compare),
please use the main [Shopsys repository](https://github.com/shopsys/shopsys).

Please, check our [Contribution Guide](https://github.com/shopsys/shopsys/blob/master/CONTRIBUTING.md) before contributing.

## Support
What to do when you are in troubles or need some help? Best way is to contact us on our Slack [http://slack.shopsys-framework.com/](http://slack.shopsys-framework.com/)

If you want to [report issues](https://github.com/shopsys/shopsys/issues/new), please use the main [Shopsys repository](https://github.com/shopsys/shopsys).

