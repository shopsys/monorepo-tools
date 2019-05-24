# Console Commands for Application Management (Phing Targets)

## Phing
[Phing](https://www.phing.info/) is a PHP project build tool with similar capabilities as GNU `make`. It can be configured via XML to install your application, run automatic tests and code standards checks, build CSS files from LESS and more.

## List of all available targets
You can list all available Phing targets by running:
```
php phing
```

## How Phing targets work
Phing targets are defined in `build.xml` and `build-dev.xml` files.
Any Phing target can execute a subset of other targets or console commands.

*Tip: You can use shorthand command `./phing <target-name>` on Unix system or `phing <target-name>` in Windows CMD instead of `php phing <target-name>`.*

Let us take `build` target for example.
It is located in `build.xml` file in the `shopsys/framework` package and it looks like this:
```xml
<target name="build" depends="build-deploy-part-1-db-independent, build-deploy-part-2-db-dependent" description="Builds application for production preserving your DB."/>
```
This means that every time you run `php phing build` the `build-deploy-part-1-db-independent` and `build-deploy-part-2-db-dependent` targets are executed.
But what do those targets do?
Let us take look at the first one, that is located in the same file:
```xml
<target name="build-deploy-part-1-db-independent" depends="clean,composer,npm,dirs-create,domains-urls-check,assets" description="First part of application build for production preserving your DB (can be run without maintenance page)."/>
```
Target `build-deploy-part-1-db-independent` also executes subset of Phing targets (`clean`,`composer`,`npm`,`dirs-create`,`domains-urls-check`,`assets`).

***Note:** During the execution of `composer and npm targets`, there will be installed 3-rd party software as dependencies of Shopsys Framework by [composer](https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies) and [npm](https://docs.npmjs.com/about-the-public-npm-registry) with licenses that are described in document [Open Source License Acknowledgements and Third-Party Copyrights](../../open-source-license-acknowledgements-and-third-party-copyrights.md)*

Let us move a little deeper and take a look at the first one, `clean`:
```xml
<target name="clean" description="Cleans up directories with cache and scripts which are generated on demand.">
    <delete failonerror="false" includeemptydirs="true">
        <fileset dir="${path.var}/cache/">
            <exclude name="/" />
        </fileset>
        <fileset dir="${path.web.scripts}/">
            <exclude name="/" />
        </fileset>
    </delete>
</target>
```
Here we can see that this target deletes all dirs in folder `/var/cache/` and `/web/scripts/`.

More information about working with Phing can be found in its [documentation](https://www.phing.info/phing/guide/en/output/hlhtml/#d5e795).

## Most Used Phing Targets
Every Phing target is a task that can be executed simply by `php phing <target-name>` command.

### Basic

#### build
Builds the application for production preserving your DB.

Most important build command for production. Cleans cache, installs composer dependencies, installs npm, install assets, installs database migrations and much more.

*Note: More about how to install and deploy your application in production can be found in [Installation Using Docker on Production Server](/docs/installation/installation-using-docker-on-production-server.md)*

#### build-demo-ci
Most important build command for continuous integration server. Builds the whole application and after that runs all coding standards checks and all tests.

*Note: More about how to build your CI and check your application there can be found in [Configuring Jenkins for Continuous Integration](/docs/cookbook/jenkins-configuration.md)*

#### build-demo-dev
Builds the application for development with clean demo DB and runs checks on changed files.

Most important build command for development. Wipes the application data, installs missing dependencies via Composer, creates clean DB and fills it with demo data, prepares assets, builds LESS into CSS, prepares error pages, checks coding standards in changed files (with changes against `origin/master`) and runs the unit, database, and smoke tests.

#### build-dev-quick
Builds the application for development preserving your DB while skipping non-essential steps.

Useful for quick migration of recently pulled changes. Cleans cache, installs missing dependencies via Composer, executes DB migrations, prepares assets and builds LESS into CSS.

#### build-demo-dev-quick
This target is useful if you have already running application and you want to quickly rebuild your application without checking coding standards, running tests, checking right timezone set.

#### server-run
Runs PHP built-in web server for a chosen domain.

This means you can see the application running without configuring Nginx or Apache server locally.

#### clean
Cleans up directories with cache and scripts which are generated on demand.

Your go-to command when you feel something should work but does not. Especially useful in the test environment in which cache is not automatically invalidated.

#### clean-redis
Cleans up cache in Redis database except for sessions.

Useful in development environment and during deploying to production.

#### clean-redis-old
Cleans up cache in Redis database for previously built versions (but keeps sessions).
Previously built version is different from current `build-version` generated by [generate-build-version](#generate-build-version).

The difference between [clean-redis](#clean-redis) is that `clean-redis` cleans only current cache, but `clean-redis-old` cleans old versions and keeps the current cache.

Useful in a development environment and during deploying to production.

#### generate-build-version

Generates a Symfony configuration `build-version` variable that is used to distinguish different application builds.
The variable itself contains current datetime in PHP format `YmdHis` (16 digits, eg. `20190311135223`) and you can use it in any configuration file by `'%build-version%'`.

The variable is generated to file `app/config/parameters_version.yml` and this file is excluded from git.

### Database

#### db-migrations-generate
Generates a new [database migration](database-migrations.md) class when DB schema is not satisfying ORM.

When you make changes to ORM entities you should run this command that will generate a new migration file for you.

#### db-migrations
Executes [database migrations](database-migrations.md) and checks schema.

#### db-create
Creates database with required db extensions and collations (that are operating system specific, unfortunately).

The target interactively asks for DB superuser credentials in order to perform all the actions so it is not needed to put superuser credentials into `app/config/parameters.yml`.

When a locale is not supported by the operating system the command explains the situation and links to the documentation.

The command is designed to be run only during the first creation of the database but as it uses `IF NOT EXISTS` commands, it can be manually run on existing database in order to create new DB extensions or collations, too.

#### db-demo
Drops all data in the database and creates a new one with demo data.

#### test-db-demo
Drops all data in the test database and creates a new one with demo data.

*Note: All database related targets `db-*` have their `test-db-*` variant for the test database.*

#### product-search-recreate-structure
Recreates Elasticsearch indexes structure.
Consists of two subtasks that can be run independently:
* `product-search-delete-structure` - deletes existing indexes structure
* `product-search-create-structure` - creates new indexes structure by json definitions stored in [the resources directory](/project-base/src/Shopsys/ShopBundle/Resources/definition).

#### product-search-export-products
Exports all visible products to Elasticsearch.

### Coding standards

#### standards / standards-diff
Checks coding standards in source files. Checking all files may take a few minutes, `standards-diff` is much quicker as it checks only files changed against `origin/master`.

#### standards-fix / standards-fix-diff
Automatically fixes some coding standards violations in all or only changed files.

### Tests

#### tests
Runs unit, database and smoke tests on a newly built test database.

Creates a new test database with demo data and runs all tests except acceptance and performance (they are more time-consuming).

#### tests-acceptance
Runs acceptance tests. Running Selenium server is required.

More on this topic can be found in [Running Acceptance Tests](./running-acceptance-tests.md).

#### selenium-run
Runs the Selenium server for acceptance testing. [ChromeDriver](https://sites.google.com/a/chromium.org/chromedriver/downloads) is required.

#### tests-performance-run
Runs performance tests on a newly built test database with performance data.

It may take a few hours as the generation of performance data is very time-consuming. Should be executed on CI server only.

The size of performance data to be generated and asserted limits can be configured via parameters defined in [`parameters_common.yml`](../../project-base/app/config/parameters_common.yml).
You can easily override the default values in your `parameters.yml` or `parameters_test.yml` configuration files.

### Other

#### cron
Runs background jobs. Should be executed periodically by system Cron every 5 minutes.

Essential for the production environment. Periodically executed Cron modules recalculate visibility, generate XML feeds and sitemaps, provide error reporting etc.

If you want to have more cron instances registered, you need to create new targets with instance specified.  
For example:
```xml
<target name="cron-default" description="Runs background jobs. Should be executed periodically by system Cron every 5 minutes.">
    <exec executable="${path.php.executable}" passthru="true" checkreturn="true">
        <arg value="${path.bin-console}" />
        <arg value="shopsys:cron" />
        <arg value="--instance-name=default" />
    </exec>
</target>

<target name="cron-import" description="Runs background jobs for import. Should be executed periodically by system Cron every 5 minutes.">
    <exec executable="${path.php.executable}" passthru="true" checkreturn="true">
        <arg value="${path.bin-console}" />
        <arg value="shopsys:cron" />
        <arg value="--instance-name=import" />
    </exec>
</target>
```

For more information, see [Working with Multiple Cron Instances](/docs/cookbook/working-with-multiple-cron-instances.md) cookbook or you can read about [Cron in general](/docs/introduction/cron.md).

#### cron-list
Lists all available background jobs. If there is more than one cron instance registered, jobs are grouped by instance.

For more information, see [Working with Multiple Cron Instances](/docs/cookbook/working-with-multiple-cron-instances.md) cookbook or you can read about [Cron in general](/docs/introduction/cron.md).

#### grunt
Builds CSS from LESS via Grunt.

Useful when modifying only LESS files.

#### dump-translations
Extracts translatable messages from the whole project including back-end.

Great tool when you want to translate your application into another language.

For more information about translations, see [the separate article](/docs/introduction/translations.md).

## Customize Phing properties (paths etc.)
You can customize any property defined in `build.xml` via configuration file `build/build.local.properties` (use `build/build.local.properties.dist` as a template).

For example, you may define the path to your installed ChromeDriver (required for running acceptance tests) on Windows by:
```
path.chromedriver.executable=C:/Tools/chromedriver.exe
```
