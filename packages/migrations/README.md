## About
This bundle provides commands which can be useful for database migrations:
* `shopsys:migrations:check-schema` checks if database schema is satisfying ORM.
* `shopsys:migrations:count` returns count of migrations to execute.
* `shopsys:migrations:migrate` executes all database migrations in one transaction.
* `shopsys:migrations:generate` generate database migrations if necessary.

This bundle uses [DoctrineMigrationsBundle](https://symfony.com/doc/current/bundles/DoctrineMigrationsBundle), so you have to install both.

## To be able to use this bundle, you need to do following:
1. In your applications `composer.json` add repository

	```
	"type": "vcs",
	"url": "https://github.com/shopsys/migrations.git"
	```
2. Require `shopsys/migrations` in `composer.json`
3. Register bundles in your `AppKernel.php`:

	```
	new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
	new ShopSys\MigrationBundle\ShopSysMigrationBundle(),
	```
4. Configure `DoctrineMigrationsBundle` according to its documentation (see https://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html#configuration)