# Running Acceptance Tests

## Prerequisites
For running acceptance tests you need to install [Google Chrome browser](https://www.google.com/chrome/browser/desktop/) and download [ChromeDriver](https://sites.google.com/a/chromium.org/chromedriver/).

You must choose compatible versions of Google Chrome and ChromeDriver.
As Chrome browser has auto-update enabled by default this may require you to update ChromeDriver from time to time.

### Installing Google Chrome browser
Download and install Google Chrome browser from: https://www.google.com/chrome/browser/desktop/

### Setting-up ChromeDriver (Selenium WebDriver)
ChromeDriver can be downloaded from: https://sites.google.com/a/chromium.org/chromedriver/downloads

Extract the executable in your system `PATH` directory.
Alternatively, you can extract it anywhere and just point to the executable from your `build/build.local.properties` file.
Example:
```
path.chromedriver.executable=C:\tools\chrome-driver\chromedriver.exe
```

## Running the whole acceptance test suite
After finishing the steps above, running acceptance tests is easy.
Just run the following commands (each in a separate terminal):
```
# run PHP web server
php phing server-run

# run Selenium server
php phing selenium-run

# run acceptance test suite
php phing tests-acceptance
```

*Note: `pg_dump` is executed internally to enable reverting the test DB to its previous state. You may have to add path of your PostgreSQL installation to the system `PATH` directory for it to work.*

*Note: If you interrupt running acceptance tests you may need to delete root file named `TEST` that is temporarily created to switch application to `TEST` environment.*

## Running individual tests
Sometimes you may want to debug individual test without running the whole acceptance test suite (which can take several minutes).

### Prepare database dump and switch to TEST environment
```
# create test database and fill it with demo data
php phing test-db-demo

# create test database dump with current data which will be restored before each test
php phing test-db-dump

# switch application to TEST environment
# on Unix systems (Linux, Mac OSX)
touch TEST
# in Windows CMD
echo.>TEST
```

### Run individual tests
```
vendor/bin/codecept run -c build/codeception.yml acceptance tests/ShopBundle/Acceptance/acceptance/OrderCest.php:testOrderCanBeCompleted
```

Do not forget to run both PHP web server and Selenium server. See [Running the whole acceptance test suite](#running-the-whole-acceptance-test-suite).

*Note: In Windows CMD you have to use backslashes in the path of the executable: `vendor\bin\codecept run ...`*

### Do not forget to restore your original environment afterwards
```
# on Unix systems (Linux, Mac OSX)
rm TEST
# in Windows CMD
del TEST
```
