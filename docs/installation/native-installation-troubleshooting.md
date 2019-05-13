# Native Installation Troubleshooting
Here are some issues you may encounter during installation and how to solve them:

## Phing target db-drop fails because database user is not an owner of schema "public"
Error message:
```
[Doctrine\DBAL\Exception\DriverException]
An exception occurred while executing 'DROP SCHEMA IF EXISTS public CASCADE':
SQLSTATE[42501]: Insufficient privilege: 7 ERROR:  must be owner of schema public
```

Default owner of schema `public` in any new database is user `postgres`.

In order to enable Phing to drop schema `public` you must change the ownership of public schema in your database by running the following command:
```
psql --username postgres --dbname <database_name> --command "ALTER SCHEMA public OWNER TO <database_user>"
psql --username postgres --dbname <test_database_name> --command "ALTER SCHEMA public OWNER TO <database_user>"
```

## Phing target db-create fails on MissingLocaleException
Error message:
```
[Shopsys\FrameworkBundle\Command\Exception\MissingLocaleException]  
It looks like your operating system does not support locale "cs_CZ.utf8". Please visit docs/installation/native-installation-troubleshooting.md for more details.

[Doctrine\DBAL\Exception\DriverException]  
An exception occurred while executing 'CREATE COLLATION pg_catalog."cs_CZ" (LOCALE="cs_CZ"."utf8")':  
SQLSTATE[22023]: Invalid parameter value: 7 ERROR:  could not create locale "cs_CZ.utf8": No such file or directory  
DETAIL:  The operating system could not find any locale data for the locale name "cs_CZ.utf8".
```

Some features like sorting products by name in the products catalog require your database to contain specific collations in order to be able to sort by locale-specific rules.
Unfortunately, in PostgreSQL locales are operating system dependent, which means that they can be different on each system.
Shopsys Framework normalizes the names of locales present in different systems by creating new collations in the database.

However, if your operating system does not provide the required locales you can try:
* On Linux: Install additional locales to your system (eg. on Debian Linux this can be done by installing [locales-all](https://packages.debian.org/cs/stable/locales-all) package) and restart the database server.
* On Windows: Make sure you use PostgreSQL distribution that supports multiple locales. We recommend to use [EnterpriseDB PostgreSQL distribution](https://www.enterprisedb.com/downloads/postgres-postgresql-downloads#windows).
* Otherwise: The only other option is to create the database collation mentioned in the exception manually using a locale that your OS supports.
(Note: every OS should support special locale `"C"` or `"POSIX"`.)

## Phing target timezones-check fails

Error message:
```
[Shopsys\FrameworkBundle\Command\Exception\DifferentTimezonesException]
  Timezones in PHP and database configuration must be identical. Current settings - PHP:UTC, PostgreSQL:Europe/Prague
```

The problem is that your `timezone` setting in PostgreSQL and `date.timezone` in `php.ini` are different.

Currently, some features are dependent on the fact that time zones in database and PHP are the same. Please set them both to the same timezone.

Timezone used in PostgreSQL can be determined by running the following command:
```
psql --username postgres --dbname <database_name> --command "SHOW timezone"
```

Timezone for PHP can be set in your `php.ini` (usually located in `/etc/php.ini`) by configuration like:
```
date.timezone = "UTC"
```

## Phing target npm fails

In some cases we encountered that node packages failed to install locally.
Nevertheless installation was able to process via global parameter.
```
npm install --global <path-to-package.json>
```

## There are no logs during installation or use of application

Monolog is configured to log into streams in [`app/config/packages/monolog.yml`](../../project-base/app/config/packages/monolog.yml).
If you want to log into a file change the configuration of handlers like this:
```yaml
monolog:
  # ...
  handlers:
    # ...
    cron:
      type: rotating_file
      max_files: 7
      path: "%kernel.logs_dir%/%kernel.environment%.cron.log"
```

## Composer dependencies installation fails on memory limit
When `composer install` or `composer update` fails on an error with exceeding the allowed memory size, you can increase the memory limit by setting `COMPOSER_MEMORY_LIMIT`.

## There is not possible to create, copy, move or remove files on local filesystem (Windows like filesystems)
When there is not possible to do some operations with files withing labeled local filesystem (`C:`, `D:`, ...), the problem could be solved by removing label part from the path to the files using `TransformString::removeDriveLetterFromPath` method.

## Still struggling with installation?
If you encountered any other problem during the installation please [file an issue](https://github.com/shopsys/shopsys/issues/new) and we will help you.
