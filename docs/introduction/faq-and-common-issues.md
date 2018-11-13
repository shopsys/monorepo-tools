# FAQ and Common Issues

This section provides only the basic answers to some of the most frequently asked questions.
For more detailed information about the Shopsys Framework, please see [Shopsys Framework Knowledge Base](../index.md).

### Index
- [What are the phing targets?](#what-are-the-phing-targets)
- [What are the data fixtures good for?](#what-are-the-data-fixtures-good-for)
- [How to change a domain URL?](#how-to-change-a-domain-url)
- [How to use database migrations and why the developers should use shopsys:migrations:generate instead of the default Doctrine one?](#how-to-use-database-migrations-and-why-the-developers-should-use-shopsysmigrationsgenerate-instead-of-the-default-doctrine-one)
- [Do I have to run coding standards check over all files?](#do-i-have-to-run-coding-standards-check-over-all-files)
- [Is the application https ready or does it need some extra setting?](#is-the-application-https-ready-or-does-it-need-some-extra-setting)
- [How can I easily translate and set up my new language constants?](#how-can-i-easily-translate-and-set-up-my-new-language-constants)
- [How to set up deployment and production server?](#how-to-set-up-deployment-and-production-server)
- [How to set up the administration with a different locale/language (e.g. Czech)?](#how-to-set-up-the-administration-with-a-different-localelanguage-eg-czech)
- [What are the differences between "listable", "sellable", "offered" and "visible" products?](#what-are-the-differences-between-listable-sellable-offered-and-visible-products)
- [How calculated attributes work?](#how-calculated-attributes-work)
- [How do I change the environment (PRODUCTION/DEVELOPMENT/TEST)?](#how-do-i-change-the-environment-productiondevelopmenttest)
- [Are some periodic tasks part of the Shopsys Framework (cron)?](#are-some-periodic-tasks-part-of-the-shopsys-framework-cron)

## What are the phing targets?
Every phing target is a task that can be executed simply by `php phing <target-name>` command.
See more about phing targets in [Console Commands for Application Management (Phing Targets)](./console-commands-for-application-management-phing-targets.md).

## What are the data fixtures good for?
Data fixtures are actually demo data available in the Shopys Framework.
There are two kinds of demo data, the singledomain and the multidomain.
For the installation of the basic demo data, use the phing target `db-fixtures-demo-singledomain`.
For the installation of the multidomain demo data, use the phing target `db-fixtures-demo-multidomain`.
These phing targets are usually triggered as the part of others phing targets, because they require the application in a certain state, for example, configured domains, an existing database structure, and so on, see `build.xml` and `build-dev.xml`.
Demo data are used for automatic tests and also for installation of demo shop with prepared data.

## How to change a domain URL?
The change of domain url requires two steps.
In the first step, you need to modify the domain url in the configuration file `app/config/domains_urls.yml`.
In the second step, you need to replace all occurrences of the old url address in the database with the new url address.
This scenario is described in more detail in the tutorial [How to Set Up Domains and Locales (Languages)](./how-to-set-up-domains-and-locales.md#4-change-the-url-address-for-an-existing-domain).

## How to use database migrations and why the developers should use shopsys:migrations:generate instead of the default Doctrine one?
Migrations (also known as database migrations) are used to unify the database schema with ORM.
On Shopsys Framework, you can use the phing target `db-migrations-generate` for migrations generation.
Compared to the standard migrations generation process from Doctrine, this phing target does not generate "irreversible" migrations, such as migrations with the operations `DROP` and `DELETE`.
Migrations are described more in detail in the docs [Database Migrations](./database-migrations.md)

## Do I have to run coding standards check over all files?
No, you do not have to.
Some of the coding standards check commands are available in two forms.
The first basic form is used to check all files.
The second additional form, commands with the suffix `-diff`, is used to check only modified files.
For example, the phing target `standards` starts checking of all files in the application while the phing target `standards-diff` starts checking only the modified files.
Modifications are detected via git by comparison against the origin/master version.

## Is the application https ready or does it need some extra setting?
Shopsys Framework is fully prepared for HTTPS.
You can just use `https://<your-domain>` in your `domains_urls.yml` configuration file.
Of course, an SSL certificate must be installed on your server.

## How can I easily translate and set up my new language constants?
To set up the user translations of labels and messages, use the files `messages.en.po` and `validators.en.po`, where `en` represents the locale.
These files are generated for each locale you use, and you can find them in the `ShopBundle/Resources/translations/` directory.
Language settings are described more in detail in the tutorial [How to Set Up Domains and Locales (Languages)](./how-to-set-up-domains-and-locales.md#3-locale-settings).

## How to set up deployment and production server?
We recommend installation using the Docker for production.
See how to install Shopsys Framework in production and how to proceed when deploying in the tutorial [Installation Using Docker on Production Server](../installation/installation-using-docker-on-production-server.md).

## How to set up the administration with a different locale/language (e.g. Czech)?
The administration uses `en` locale by default.
If you want to switch it to the another locale, override the method `getAdminLocale()` of the class `Shopsys\FrameworkBundle\Model\Localization\Localization`.
This scenario is described in more detail in the tutorial [How to Set Up Domains and Locales (Languages)](./how-to-set-up-domains-and-locales.md#36-locale-in-administration).

## What are the differences between "listable", "sellable", "offered" and "visible" products?
Products can be grouped into several groups according to their current status or according to what they are used for.
These groups are described in more detail in the article [How to Work with Products](./how-to-work-with-products.md).

## How calculated attributes work?
Some attributes that are used on the Shopsys Framework are not set directly, but their value is automatically calculated based on other attributes.
For example, if a category of products does not have a name for a locale of the specific domain, this category will be automatically set as not visible on this domain.
See more about calculated attributes in the article [How to Work with Products](./how-to-work-with-products.md).

## How do I change the environment (PRODUCTION/DEVELOPMENT/TEST)?
The environment is determined by the existence of the files `PRODUCTION`, `DEVELOPMENT`, `TEST` in the root of your project.
This file is created automatically during the run of a command `composer install` (it will prompt you to decide, default option is `DEVELOPMENT`).

You can change the environment manually by using the command `php bin/console shopsys:environment:change`.

## Are some periodic tasks part of the Shopsys Framework (cron)?
Yes, there is some prepared configuration for Shopsys Framework cron commands in a file `src/Resources/config/services/cron.yml` in `FrameworkBundle`.
Do not forget to set up a cron on your server to execute [`php phing cron`](/docs/introduction/console-commands-for-application-management-phing-targets.md#cron) every 5 minutes.
