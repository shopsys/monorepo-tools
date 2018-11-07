# Database Migrations

In Shopsys Framework we use [Doctrine migrations bundle](https://symfony.com/doc/master/bundles/DoctrineMigrationsBundle/index.html) with a few modifications.
Our [MigrationsBundle](https://hithub.com/shopsys/migrations) supports the installation of database migrations from all registered bundles and also enables controlling their order or even skipping some.

## Where to store migrations

Migrations should be saved in a directory `Migrations` in the root of any registered bundle.

Just use the namespace of your bundle with `Migrations` at the end (eg. `\Shopsys\FrameworkBundle\Migrations`) and extend the class `\Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration`.
From now on, the migrations will be automatically registered for installation.

This allows modules to have their own database migrations making them easier to install and use.

## Generating migrations automatically

Whenever you create a new entity or edit some existing entity, you need to create a migration in order to update your database structure.
You can do this automatically via a [Console Commands for Application Management (Phing Targets)](console-commands-for-application-management-phing-targets.md).
Just run `php phing db-migrations-generate` in your console.
A new migration will be generated in the correct namespace.
You should always check the generated migrations, sometimes a minor manual change is required.

*Note: If you are developing more than one bundle you will be prompt to select one as a target.*

## Running migrations

When you add a new migration you have to execute it in order to update our database schema.
Run `php phing db-migrations` in your console and your database schema will be updated.
If the migrations fail by either triggering an error or by not passing a schema check, all migrations will be reverted by a transaction rollback.

## Locking the order of migrations

Shopsys Framework enables installation of migrations from multiple sources like your project, the framework itself, and other installed bundles.
Usually, the migrations are executed in the same order as they were generated (the class names of migrations contain the time of their creation).
This wouldn't work as well in Shopsys Framework, because you can install a module with migrations created in the past, leading to an inconsistent order of execution:

> Imagine there is an application with two installed migrations: `Version20180101115739` and `Version20180619154622`.
> Then a module with a migration `Version20180228141735` is installed.
>
> This means that the migration `Version20180228141735` was executed after `Version20180619154622` even though it was created before it.
> If the application would be installed on some other computer the migrations would be executed in a different order and might end up with different structure or data.

This problem is solved by the [MigrationsBundle](https://hithub.com/shopsys/migrations) by generating a `migrations-lock.yml`.
After the first execution of migrations, the `migrations-lock.yml` file is created in your project's root directory.
You should commit this file similarly as you [commit the Composer lock file](https://getcomposer.org/doc/01-basic-usage.md#commit-your-composer-lock-file-to-version-control).
It contains the info about all migrations that were executed and it will be used when running the migrations on a different database.

Even if you install a new module that has migrations with an older date, they will be executed last.

## Reordering and skipping migrations

The `migrations-lock.yml` file is updated automatically and it usually doesn't require any manual changes.

But, because of the differences among different modules and the customizations of your project, you may encounter a migration that collides with an already installed one.
In order to solve such a problem, you can change the order in which migrations will be installed by manually editing the file.

It is in *YAML* format and it has a simple structure - it's just an array indexed by the version number and each item has keys `class` with the migration's FQCN and `skip` with a boolean value.
The migrations will always be executed in the same order as they are listed in the file.
By setting the `skip value` to `true` you can even prevent the migration from being executed and you can implement your own migration with the wanted changes.

This means the control of the execution of migrations on your project is ultimately in your own hands.
