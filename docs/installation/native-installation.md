# Native Installation
This guide provides instructions how to install Shopsys Framework on your local machine as a server.
If you would like to use a prepared Docker container instead go to [Installation Using Docker](installation-using-docker.md).

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
composer create-project shopsys/project-base --stability=alpha --keep-vcs
cd project-base
```

*Notes:* 
- *The `--keep-vcs` option initializes GIT repository in your project folder that is needed for diff commands of the application build and keeps the GIT history of `shopsys/project-base`.*
- *The `--stability=alpha` option enables you to install the project from the last alpha release. Default value for the option is `stable` but there is no stable release yet.*

### 2. Set up configuration for native installation
Monolog is configured to log into streams in `app/config/config.yml`.
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

### 3. Install dependencies and configure parameters
Composer will prompt you to set main parameters (`app/config/parameters.yml`):

| Name                              | Description                                                                   | Default value |
| --------------------------------- | ----------------------------------------------------------------------------- | ------------- |
| `database_host`                   | access data of your PostgreSQL database                                       | 127.0.0.1     |
| `database_port`                   | ...                                                                           | 5432          |
| `database_name`                   | ...                                                                           | shopsys       |
| `database_user`                   | ...                                                                           | postgres      |
| `database_password`               | ...                                                                           | ...           |
| `database_server_version`         | version of your PostgreSQL server                                             | 9.5           |
| `redis_host`                      | host of your Redis storage (credentials are not supported right now)          | 127.0.0.1     |
| `mailer_transport`                | access data of your mail server                                               | ...           |
| `mailer_host`                     | ...                                                                           | ...           |
| `mailer_user`                     | ...                                                                           | ...           |
| `mailer_password`                 | ...                                                                           | ...           |
| `mailer_disable_delivery`         | set to `true` if you don't want to send any e-mails                           | ...           |
| `mailer_master_email_address`     | set if you want to send all e-mails to one address (useful for development)   | ...           |
| `mailer_delivery_whitelist`       | set if you want to have master e-mail but allow sending to specific addresses | ...           |
| `secret`                          | randomly generated secret token                                               | ...           |

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

#### Choose environment type
For development choose `n` when asked `Build in production environment? (Y/n)`.

It will set the environment in your application to `dev` (this will, for example, show Symfony Web Debug Toolbar).

### 4. Configure domains
Create `domains_urls.yml` from `domains_urls.yml.dist`.

#### Linux / MacOS
```
cp app/config/domains_urls.yml.dist app/config/domains_urls.yml
```

#### Windows
```
copy app\config\domains_urls.yml.dist app\config\domains_urls.yml
```

### 5. Create databases
```
php phing db-create
php phing test-db-create
```
### 6. Build application
```
php phing build-demo-dev
php phing img-demo
```
**For solutions to commonly encountered problems during build see section [Troubleshooting](#troubleshooting) below or you might want to check [Required PHP Configuration](../introduction/required-php-configuration.md).**

*Tip: See introduction into [Phing Targets](../introduction/phing-targets.md) to learn how can you easily accomplish some common tasks.*

### 7. Run integrated HTTP server
```
php bin/console server:run
```
*Note: If you did not use default domain URLs in step 4 you should run `php bin/console server:run <your-domain-address>`.*

### 8. See it in your browser!
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

### Sorting is different on different platforms
This is known PostgreSQL issue, PostgreSQL uses the collation implementation from the OS.
As a result, sorting can be different on different platforms.
For example, results sorted by name can begin with a product starting with “A” (Apple iPhone 5S) on iOS and with “1” (100 Czech crowns) on Linux.

This problem only occurs if the installation is native and it should be solved in PostgreSQL v10, when the sorting will be solved by the independent ICU library. 

Installation using docker provides a unified environment for all platforms as a result of which the sorting is the same.

### Still struggling with installation?
If you encountered any other problem during the installation please [file an issue](https://github.com/shopsys/shopsys/issues/new) and we will help you.
