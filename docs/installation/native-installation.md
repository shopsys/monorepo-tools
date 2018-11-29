# Native Installation
This guide provides instructions how to install Shopsys Framework on your local machine as a server.
If you would like to use a prepared Docker container instead go to [Installation Using Docker](installation-using-docker.md).

*This installation guide is not tested due to experimental microservices development.*

## Requirements
First of all, you need to install the following software on your system:

### Linux / MacOS
* [GIT](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
* [PostgreSQL 9.5](https://wiki.postgresql.org/wiki/Detailed_installation_guides)
* [PHP 7.1 - 7.2](http://php.net/manual/en/install.php) (configure your `php.ini` by [Required PHP Configuration](../introduction/required-php-configuration.md))
* [Composer](https://getcomposer.org/doc/00-intro.md#globally)
* [Node.js with npm](https://nodejs.org/en/download/) (npm is automatically installed when you install Node.js)
* [Redis](https://redis.io/topics/quickstart)
* [Elasticsearch](https://www.elastic.co/guide/en/elasticsearch/reference/current/install-elasticsearch.html)

### Windows
* [GIT](https://git-scm.com/download/win)
* [PostgreSQL 9.5](https://www.enterprisedb.com/downloads/postgres-postgresql-downloads#windows)
* [PHP 7.1 - 7.2](http://php.net/manual/en/install.windows.php) (configure your `php.ini` by [Required PHP Configuration](../introduction/required-php-configuration.md))
* [Composer](https://getcomposer.org/doc/00-intro.md#installation-windows)
* [Node.js with npm](https://nodejs.org/en/download/) (npm is automatically installed when you install Node.js)
* [Redis](https://github.com/MicrosoftArchive/redis/releases)
* [Elasticsearch](https://www.elastic.co/guide/en/elasticsearch/reference/current/install-elasticsearch.html)

*Note: The names link to the appropriate installation guide or download page.*

After that, you can follow the steps below in order to install and configure Shopsys Framework.

## Steps
### 1. Create new project from Shopsys Framework sources
```
composer create-project shopsys/project-base --stability=beta --keep-vcs
cd project-base
```

*Notes:*
- *The `--keep-vcs` option initializes GIT repository in your project folder that is needed for diff commands of the application build and keeps the GIT history of `shopsys/project-base`.*
- *The `--stability=beta` option enables you to install the project from the last beta release. Default value for the option is `stable` but there is no stable release yet.*

### 2. Set up configuration for native installation
#### 2.1. Set up logging
Monolog is configured to log into streams in `app/config/packages/monolog.yml`.
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

#### 2.2. Set up the microservice for product search
For the product search to be working correctly, you'll have to install a [microservice for product search](https://github.com/shopsys/microservice-product-search).
It will act as a fully independent unit with a separate web server and repository.

Clone the repository into a separate directory:
```
git clone https://github.com/shopsys/microservice-product-search.git
cd microservice-product-search
```

Require symfony webserver:

```
composer require-dev symfony/web-server-bundle
```

Install composer dependencies:

```
composer install
```

Configure connection to the Elasticsearch by setting up the ELASTICSEARCH_HOSTS_STRING environment variable (or the [.env file](http://symfony.com/doc/current/components/dotenv.html)) and run the server:

```
php bin/console server:run 127.0.0.1:8001
```

*Note: If you use other port for the microservice to run, you'll have to pass its URL to the application as a parameter `microservice_product_search_url`.*

In this moment the microservice is ready for the requests processing.

#### 2.3. Set up the microservice for product search export
You have to install also [microservice for product search export](https://github.com/shopsys/microservice-product-search-export).
The installation is as same as for product search microservice. In short, it is:

```
git clone https://github.com/shopsys/microservice-product-search-export.git
cd microservice-product-search-export

composer require-dev symfony/web-server-bundle

composer install
```

Configure connection to the Elasticsearch by setting up the ELASTICSEARCH_HOSTS_STRING environment variable.

```
php bin/console server:run 127.0.0.1:8002
```

*Note: If you use other port for the microservice to run, you'll have to pass its URL to the application as a parameter `microservice_product_search_export_url`.*

### 3. Install dependencies and configure parameters
Composer will prompt you to set main parameters (`app/config/parameters.yml`):

| Name                                     | Description                                                                   | Default value |
| ---------------------------------        | ----------------------------------------------------------------------------- | ------------- |
| `database_host`                          | access data of your PostgreSQL database                                       | 127.0.0.1     |
| `database_port`                          | ...                                                                           | 5432          |
| `database_name`                          | ...                                                                           | shopsys       |
| `database_user`                          | ...                                                                           | postgres      |
| `database_password`                      | ...                                                                           | ...           |
| `database_server_version`                | version of your PostgreSQL server                                             | 10.5          |
| `redis_host`                             | host of your Redis storage (credentials are not supported right now)          | 127.0.0.1     |
| `mailer_transport`                       | access data of your mail server                                               | ...           |
| `mailer_host`                            | ...                                                                           | ...           |
| `mailer_user`                            | ...                                                                           | ...           |
| `mailer_password`                        | ...                                                                           | ...           |
| `mailer_disable_delivery`                | set to `true` if you don't want to send any e-mails                           | ...           |
| `mailer_master_email_address`            | set if you want to send all e-mails to one address (useful for development)   | ...           |
| `mailer_delivery_whitelist`              | set if you want to have master e-mail but allow sending to specific addresses | ...           |
| `microservice_product_search_url`        | URL of the product search microservice                                        | http://127.0.0.1:8001 |
| `microservice_product_search_export_url` | URL of the product search export microservice                                 | http://127.0.0.1:8002 |
| `secret`                                 | randomly generated secret token                                               | ...           |
| `trusted_proxies`                        | proxies that are trusted to pass traffic, used mainly for production          | [127.0.0.1] |

Composer will then prompt you to set parameters for testing environment (`app/config/parameters_test.yml`):

| Name                              | Description                                                                   | Default value |
| --------------------------------- | ----------------------------------------------------------------------------- | ------------- |
| `test_database_host`              | access data of your PostgreSQL database for tests                             | 127.0.0.1     |
| `test_database_port`              | ...                                                                           | 5432          |
| `test_database_name`              | ...                                                                           | shopsys-test  |
| `test_database_user`              | ...                                                                           | postgres      |
| `test_database_password`          | ...                                                                           | ...           |
| `overwrite_domain_url`            | overwrites URL of all domains for acceptance testing (set to `~` to disable)  | ~             |
| `selenium_server_host`            | with native installation the selenium server is on `localhost`                | 127.0.0.1     |
| `test_mailer_transport`           | access data of your mail server for tests                                     | ...           |
| `test_mailer_host`                | ...                                                                           | ...           |
| `test_mailer_user`                | ...                                                                           | ...           |
| `test_mailer_password`            | ...                                                                           | ...           |
| `shopsys.content_dir_name`        | web/content-test/ directory is used instead of web/content/ during the tests  | content-test  |


### 4. Create databases
```
php phing db-create
php phing test-db-create
```

*Note: In this step you were using multiple Phing targets.
More information about what Phing targets are and how they work can be found in [Console Commands for Application Management (Phing Targets)](/docs/introduction/console-commands-for-application-management-phing-targets.md)*

### 5. Build application
```
php phing build-demo-dev
```
**For solutions to commonly encountered problems during build see section [Troubleshooting](#troubleshooting) below or you might want to check [Required PHP Configuration](../introduction/required-php-configuration.md).**

### 6. Run integrated HTTP server
```
php bin/console server:run
```

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

### Phing target db-create fails on MissingLocaleException
Error message:
```
[Shopsys\FrameworkBundle\Command\Exception\MissingLocaleException]  
It looks like your operating system does not support locale "cs_CZ.utf8". Please visit docs/installation/native-installation.md#troubleshooting for more details.

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

### Phing target timezones-check fails

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

### Still struggling with installation?
If you encountered any other problem during the installation please [file an issue](https://github.com/shopsys/shopsys/issues/new) and we will help you.
