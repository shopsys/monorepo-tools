# Phing Targets

## Phing
[Phing](https://www.phing.info/) is a PHP project build tool with similar capabilities as GNU `make`. It can be configured via XML (see `build.xml` and `build-dev.xml`) to install your application, run automatic tests and code standards checks, build CSS files from LESS and more.

## List of all available targets
You can list all available Phing targets by running:
```
php phing -l
```

## Targets
Every Phing target is a task that can be executed simply by `php phing <target-name>` command.

*Tip: You can use shorthand command `./phing <target-name>` on Unix system or `phing <target-name>` in Windows CMD instead of `php phing <target-name>`.*

### Basic

#### build-demo-dev
Builds the application for development with clean demo DB and runs checks on changed files.

Most important build command for development. Wipes the application data, installs missing dependencies via Composer, creates clean DB and fills it with demo data, prepares assets, builds LESS into CSS, prepares error pages, checks coding standards in changed files (with changes against `origin/master`) and runs the unit, database, and smoke tests.

#### build-dev-quick
Builds the application for development preserving your DB while skipping non-essential steps.

Useful for quick migration of recently pulled changes. Cleans cache, installs missing dependencies via Composer, executes DB migrations, prepares assets and builds LESS into CSS.

#### server-run
Runs PHP built-in web server for a chosen domain.

This means you can see the application running without configuring Nginx or Apache server locally.

#### clean
Cleans up directories with cache and scripts which are generated on demand.

Your go-to command when you feel something should work but does not. Especially useful in the test environment in which cache is not automatically invalidated.

### Database

#### db-migrations-generate
Generates migration file when DB schema is not satisfying ORM.

When you make changes to ORM entities you should run this command that will generate a new migration file for you.

#### db-migrations
Executes database migrations and checks schema.

#### db-demo
Drops all data in the database and creates a new one with demo data.

#### test-db-demo
Drops all data in the test database and creates a new one with demo data.

#### img-demo
Installs demo images.

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

More on this topic can be found in [Running Acceptance Tests](running-acceptance-tests.md).

#### selenium-run
Runs the Selenium server for acceptance testing. [ChromeDriver](https://sites.google.com/a/chromium.org/chromedriver/downloads) is required.

#### tests-performance-run
Runs performance tests on a newly built test database with performance data.

It may take a few hours as the generation of performance data is very time-consuming. Should be executed on CI server only.

### Other

#### cron
Runs background jobs. Should be executed periodically by system Cron every 5 minutes.

Essential for the production environment. Periodically executed Cron modules recalculate visibility, generate XML feeds and sitemaps, provide error reporting etc.

#### cron-list
Lists all available background jobs.

#### grunt
Builds CSS into LESS via Grunt.

Useful when modifying only LESS files.

#### dump-translations
Extracts translatable messages from the whole project including back-end.

Great tool when you want to translate your application into another language.

## Customize Phing properties (paths etc.)
You can customize any property defined in `build.xml` via configuration file `build/build.local.properties` (use `build/build.local.properties.dist` as a template).

For example, you may define the path to your installed ChromeDriver (required for running acceptance tests) on Windows by:
```
path.chromedriver.executable=C:/Tools/chromedriver.exe
```
