# Installation Guide

## Requirements
* [GIT](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
* [PostgreSQL 9.4](https://wiki.postgresql.org/wiki/Detailed_installation_guides)
* [PHP 7.x](http://php.net/manual/en/install.php) (see [Recommended PHP Configuration](recommended-php-configuration.md))
* [Composer](https://getcomposer.org/doc/00-intro.md#globally)
* [Node.js 4.x](https://nodejs.org/en/download/)
* [npm 2.x](https://nodejs.org/en/download/)

## Steps
### 1. Get source code
```
git clone https://git.shopsys-framework.com/shopsys/shopsys-framework.git
cd shopsys-framework
```

### 2. Create databases
```
createdb <database-name>
createdb <test-database-name>
```

### 3. Install dependencies and configure parameters
```
composer install
```

Composer will prompt you to set main parameters (`app/config/parameters.yml`):

| Name                              | Description                                                                   |
| --------------------------------- | ----------------------------------------------------------------------------- |
| `database_host`                   | access data of your PostgreSQL database                                       |
| `database_port`                   | ...                                                                           |
| `database_name`                   | ...                                                                           |
| `database_user`                   | ...                                                                           |
| `database_password`               | ...                                                                           |
| `mailer_transport`                | access data of your mail server                                               |
| `mailer_host`                     | ...                                                                           |
| `mailer_user`                     | ...                                                                           |
| `mailer_password`                 | ...                                                                           |
| `mailer_disable_delivery`         | set to `true` if you don't want to send any e-mails                           |
| `mailer_master_email_address`     | set if you want to send all e-mails to one address (useful for development)   |
| `mailer_delivery_whitelist`       | set if you want to have master e-mail but allow sending to specific addresses |
| `email_for_error_reporting`       | e-mail address that will be used for error reports                            |
| `secret`                          | randomly generated secret token                                               |

Composer will then prompt you to set parameters for testing environment (`app/config/parameters_test.yml`):

| Name                              | Description                                                                   |
| --------------------------------- | ----------------------------------------------------------------------------- |
| `test_database_host`              | access data of your PostgreSQL database for tests                             |
| `test_database_port`              | ...                                                                           |
| `test_database_name`              | ...                                                                           |
| `test_database_user`              | ...                                                                           |
| `test_database_password`          | ...                                                                           |
| `test_mailer_transport`           | access data of your mail server for tests                                     |
| `test_mailer_host`                | ...                                                                           |
| `test_mailer_user`                | ...                                                                           |
| `test_mailer_password`            | ...                                                                           |
| `email_for_error_reporting`       | e-mail address that will be used for error reports during tests               |

#### Choose environment type
For development choose `n` when asked `Build in production environment? (Y/n)`.

It will set environment in your application to `dev` (this will for example show Symfony Web Debug Toolbar).

### 4. Configure domains
Create `domains_urls.yml` from `domains_urls.yml.dist`.
```
cp app/config/domains_urls.yml.dist app/config/domains_urls.yml
```

### 5. Build application
```
php phing build-demo-dev
php phing img-demo
```
**For solutions to commonly encountered problems during build see section [Troubleshooting](#troubleshooting) below.**

*Tip: See introduction into [Phing Targets](phing-targets.md) to learn how can you easily accomplish some common tasks.*

### 6. Run integrated HTTP server
```
php bin/console server:run
```
*Note: If you did not use default domain URLs in step 4 you should run `php bin/console server:run <your-domain-address>`.*

### 7. See it in your browser!
Open [http://127.0.0.1:8000/](http://127.0.0.1:8000/) to see running application.

You can also login into the administration section on [http://127.0.0.1:8000/admin/](http://127.0.0.1:8000/admin/) with default credentials:
* Username: `admin` or `superadmin` (the latter has access to advanced options)
* Password: `admin123`

## Troubleshooting
Here are some issues you may encounter during installation and how to solve them: 

### Phing target db-drop fails because database user is not an owner of schema "public"
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

### Phing target db-fixtures-demo-singledomain fails because DbFunctionsDataFixture cannot create extension "unaccent"
Error message:
```
[Doctrine\DBAL\Exception\DriverException]
An exception occurred while executing 'CREATE EXTENSION IF NOT EXISTS unaccent WITH SCHEMA pg_catalog':
SQLSTATE[42501]: Insufficient privilege: 7 ERROR:  permission denied to create extension "unaccent"
HINT:  Must be superuser to create this extension.
```

In order to create database extensions in PostgreSQL, one needs `superuser` role. By default, only `postgres` database user has this role.

You may not want to run the application under database user with such escalated privileges. Fortunately it is sufficient to run the following the following commands with `superuser` role and then `DbFunctionsDataFixture` will no longer try to create the extension during application build:
```
psql --username postgres --dbname <database_name> --command "CREATE EXTENSION IF NOT EXISTS unaccent WITH SCHEMA pg_catalog"
psql --username postgres --dbname <test_database_name> --command "CREATE EXTENSION IF NOT EXISTS unaccent WITH SCHEMA pg_catalog"
```

### Phing target db-fixtures-demo-singledomain fails because DbCollationsDataFixture cannot create locale
Error message:
```
[Doctrine\DBAL\Exception\DriverException]
An exception occurred while executing 'CREATE COLLATION "cs_CZ" (LOCALE="cs_CZ.utf8")':
SQLSTATE[22023]: Invalid parameter value: 7 ERROR:  could not create locale "cs_CZ.utf8": No such file or directory
DETAIL:  The operating system could not find any locale data for the locale name "cs_CZ.utf8".
```

Some features like sorting products by name in the catalogue require your operating system to support different locales in order to be able to sort by locale-specific rules.
`Shopsys\ShopBundle\DataFixtures\Base\DbCollationsDataFixture` class normalizes the names of locales present in different systems by creating new collations in the database.

However, if your system does not provide the locales mentioned in `DbCollationsDataFixture` you need to either install additional locales to your system (eg. on Debian Linux this can be done by installing [locales-all](https://packages.debian.org/cs/stable/locales-all) package) or change `DbCollationsDataFixture` to use locales present in your system.

### Phing target tests-db fails on test AdministratorRepositoryTest::testGetByValidMultidomainLogin
Error message:
```
1) Tests\ShopBundle\Database\Model\Administrator\AdministratorRepositoryTest::testGetByValidMultidomainLogin
Shopsys\ShopBundle\Model\Administrator\Security\Exception\InvalidTokenException: Administrator with valid multidomain login token validMultidomainLoginToken not found.

.../src/Shopsys/ShopBundle/Model/Administrator/AdministratorRepository.php:69
.../tests/ShopBundle/Database/Model/Administrator/AdministratorRepositoryTest.php:25
```

The problem is that your `timezone` setting in PostgreSQL and `date.timezone` in `php.ini` are different.

Currently, some features are dependent on the fact that timezones in database and PHP are the same. Please set them both to the same timezone.

Timezone used in PostgreSQL can be determined by running the following command:
```
psql --username postgres --dbname <database_name> --command "SHOW timezone"
```

Timezone for PHP can be set in your `php.ini` (usually located in `/etc/php.ini`) by configuration like:
```
date.timezone = "UTC"
```

### Still struggling with installation?
If you encountered any other problem during the installation please [file an issue](https://git.shopsys-framework.com/shopsys/shopsys-framework/issues/new) and we will help you.
