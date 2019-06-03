# Application Requirements
To be able to install, develop and run Shopsys Framework, the system should have preinstalled some tools and services.

## Linux / MacOS / WSL
* [GIT](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
* [PostgreSQL 10.5](https://wiki.postgresql.org/wiki/Detailed_installation_guides)
* [PHP 7.1 - 7.3](http://php.net/manual/en/install.php) (configure your `php.ini` by [Required PHP Configuration](../introduction/required-php-configuration.md))
* [Composer](https://getcomposer.org/doc/00-intro.md#globally)
* [Node.js with npm](https://nodejs.org/en/download/) (npm is automatically installed when you install Node.js)
* [Redis](https://redis.io/topics/quickstart)
* [Elasticsearch](https://www.elastic.co/guide/en/elasticsearch/reference/current/install-elasticsearch.html)
    * [Java SDK](https://www.oracle.com/technetwork/java/javase/overview/index.html)
    * [ICU Analysis plugin](https://www.elastic.co/guide/en/elasticsearch/plugins/current/analysis-icu.html)
* [SMTP server](https://github.com/mailhog/MailHog)
* [Selenium Server](https://www.npmjs.com/package/selenium-standalone#install--run)
* (*optional*) [Nginx](http://nginx.org/en/docs/install.html)

## Windows
* [GIT](https://git-scm.com/download/win)
* [PostgreSQL 10.5](https://www.enterprisedb.com/downloads/postgres-postgresql-downloads#windows)
* [PHP 7.1 - 7.3](http://php.net/manual/en/install.windows.php) (configure your `php.ini` by [Required PHP Configuration](../introduction/required-php-configuration.md))
* [Composer](https://getcomposer.org/doc/00-intro.md#installation-windows)
* [Node.js with npm](https://nodejs.org/en/download/) (npm is automatically installed when you install Node.js)
* [Redis](https://github.com/MicrosoftArchive/redis/releases)
* [Elasticsearch](https://www.elastic.co/guide/en/elasticsearch/reference/current/install-elasticsearch.html)
    * [Java SDK](https://www.oracle.com/technetwork/java/javase/overview/index.html)
    * [ICU Analysis plugin](https://www.elastic.co/guide/en/elasticsearch/plugins/current/analysis-icu.html)
* [SMTP server](https://www.hmailserver.com/)
* [Selenium Server](https://www.npmjs.com/package/selenium-standalone#install--run)
* (*optional*) [Nginx](http://nginx.org/en/docs/install.html)

*Notes:*
- *The names link to the appropriate installation guide or download page.*  
- ***optional Nginx can be used as replacement of standalone symfony server started via `php phing server-run`***
- *Required tools can be easily installed on Windows via [choco](https://chocolatey.org/) package manager*  
- ***Windows installation can be skipped in favor of [Windows Subsystem for Linux (WSL)](https://docs.microsoft.com/en-us/windows/wsl/install-win10) where the tools will be installed like on [Linux / MacOS / WSL](#linux--macos--wsl) so all the tools and services can be up-to-date.***
