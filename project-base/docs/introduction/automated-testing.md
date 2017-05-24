# Automated Testing

Testing is a crucial part in development and maintenance of reliable software.
For this reason Shopsys Framework comes with 5 types of automated tests:
* [Unit tests](#unit-tests)
* [Database tests](#database-tests-aka-integration-tests)
* [HTTP smoke tests](#http-smoke-tests)
* [Acceptance tests](#acceptance-tests-aka-functional-tests-or-selenium-tests)
* [Performance tests](#performance-tests)

Software testing in general is very broad topic and requires learning and practice.
The following paragraphs should help you in your path to answering questions like: *"What should I test?"*, *"Which type of tests should I use for this particular functionality?"* or *"How many tests is enough?"* 

## The purpose(s) of automated tests

For code that you are currently writing, tests can give you immediate feedback that your code works.
In connection with *tests-first* approach (eg. *Test-Driven Development*) tests also help you design your code because you focus on how the production code will be used before you write it.

Existing tests give you the confidence for making changes and refactoring without breaking things.
They will notify you when something that previously worked no longer works and help you localize the error.

Tests can also help you when reading new code written by someone else. Tests can be seen as a runnable documentation that shows how the code should be used.
High level tests (eg. [acceptance tests](#acceptance-tests-aka-functional-tests-or-selenium-tests)) can be used to discover how users can interact with the application.

## Rules of thumb for what should be tested

### Is the functionality critical for your business?
If the answer is yes then you should test the feature thoroughly.
You should write automatic tests for all crucial scenarios.

You can even test the part using multiple types of tests (eg. both [unit tests](#unit-tests) and [acceptance tests](#acceptance-tests-aka-functional-tests-or-selenium-tests)).
Let's say that we consider promo codes to be a crucial part of the business.
There will be two types of promo codes: fixed price (eg. $10 from the total price) and percentage (eg. 15% from the total price).  
We could write just unit tests for calculation of the discount for both of these promo code types.
But if working promo codes are really important for us we should also write an end-to-end acceptance test that will verify that user can add a promo code to the order and that the discount is really applied in a created order.

### Does some part of the application break often?
You may have already encountered a situation when some feature of a software used to work properly but is broken in current release.
This type of issue is so common that it even has its own name - *a regression bug*.

In ideal world every feature is tested from the beginning so regression bugs do not arise.
But in reality it is very hard (and costly) to test every aspect of your application.

However, if you run into a bug in a feature that used to work before it is a good sign that the code implementing the feature is brittle and should be verified by tests.
Also, nobody wants angry users to repeatedly report the same bug that was already fixed once.
It is a good practice to write tests that verify fixing of regression bugs.

### Do you want to refactor some existing functionality?
Refactoring is a process of enhancing code quality without changing its functionality.

If you want to refactor some part of your application you should have automatic tests beforehand in order to be sure that you did not break the application during the refactoring.

### Does your code depend on undocumented features?
When your application depends on some specific feature in 3rd party system that is not documented you can write tests to verify the expected behavior.

The fact that the feature is not documented may indicate that the authors did not consider the behavior a real feature and may change in future versions. If it does you will be notified by your tests.

## Types of automated tests available in Shopsys Framework

### Unit tests
Used to test the smallest possible amount of code (the "unit", i.e. class / method). To isolate tested unit it is useful to mock other objects - create a dummy object mimicking the real implementation of collaborating classes.

Unit tests in Shopsys Framework are built on [PHPUnit testing framework](https://phpunit.de/).

#### Advantages:
* execution is really fast
* precise localization of the problem

#### Disadvantages:
* tested code must be designed in a specific way (eg. using *dependency injection principle*)
* mocking sometimes leads to unintuitive behavior (eg. returning `null` when not expected)

#### Great for:
* testing isolated components with clear responsibilities
* testing edge cases (using large data sets)
* test driven development

#### Example:
See test class [`\Tests\ShopBundle\Unit\Model\Cart\CartServiceTest`](../../tests/ShopBundle/Unit/Model/Cart/CartServiceTest.php).
Notice that test method names describe the tested scenario. Also notice that each test case focuses just on one specific behavior of the class.
When a test fails it provides detailed feedback to the developer.

### Database tests (a.k.a. integration tests)
Even when all parts are working it is not guaranteed they work well together. Mocking can still be used for isolation when appropriate.

These tests use a separate database to not affect your application data so you can still use the application in *DEVELOPMENT* environment. It is still **not recommended** to run tests on production server because things like filesystem are shared among all kernel environments.

All tests are isolated from each other thanks to database transactions. This means they can be executed in any order as each have the same starting conditions.

#### Advantages:
* demo data can be used for testing with [`PersistentReferenceFacade`](../../src/Shopsys/ShopBundle/Component/DataFixture/PersistentReferenceFacade.php)

#### Disadvantages:
* arranging the testing data is typically more complex than in unit tests

#### Great for:
* higher level testing of collaboration of units
* low level testing of components that are hard to unit-test

#### Example:
See test class [`\Tests\ShopBundle\Database\Model\Cart\CartFacadeTest`](../../tests/ShopBundle/Database/Model/Cart/CartFacadeTest.php). Notice usage of demo data instead of preparing own entities.
  
### HTTP smoke tests
Test HTTP codes returned by individual controller actions provided by the routing (e.g. product detail page should return *200 OK* for a visible product and *404 Not Found* for a hidden one).

They help you prevent breaking your application by checking very wide scope of the application.
You will no longer cause *500 Server Error* on some random page by a seemingly unrelated change.

#### Advantages:
* all new controller actions are checked automatically (almost maintenance free)

#### Disadvantages:
* validate only HTTP codes, not the actual contents

#### Great for:
* protection from unhandled exceptions in controller actions

#### Example:
See configuration of HTTP smoke (and [performance](#performance-tests)) tests in [`\Tests\ShopBundle\Smoke\Http\UrlsProvider`](../../tests/ShopBundle/Smoke/Http/UrlsProvider.php).

### Acceptance tests (a.k.a. functional tests or Selenium tests)
Provide a way of fully end-to-end testing your application as if a real human used it.

Built on [Selenium](http://www.seleniumhq.org/) and [Codeception](http://codeception.com/), running in [Google Chrome](https://www.google.com/chrome/) browser.

More information can be found in [Running Acceptance Tests](running-acceptance-tests.md).

#### Advantages:
* end-to-end testing
* cover errors that occur only in the browser
* can test JavaScript code
* demo data can be used for testing with [`PersistentReferenceFacade`](../../src/Shopsys/ShopBundle/Component/DataFixture/PersistentReferenceFacade.php)

#### Disadvantages:
* take a while to execute
* whole application is switched to *TEST* environment
* occasional false negative reports (due to WebDriver brittleness)
* requires installation of [Google Chrome](https://www.google.com/chrome/browser/desktop/) and [ChromeDriver](https://sites.google.com/a/chromium.org/chromedriver/)

#### Great for:
* validating business-critical scenarios (eg. order creation)

#### Example:
See acceptance test for product filter in administration in [`\Tests\ShopBundle\Acceptance\acceptance\AdminProductAdvancedSearchCest`](../../tests/ShopBundle/Acceptance/acceptance/AdminProductAdvancedSearchCest.php). Notice the usage of auto-wired Page objects [`LoginPage`](../../tests/ShopBundle/Acceptance/acceptance/PageObject/Admin/LoginPage.php) and [`ProductAdvancedSearchPage`](../../tests/ShopBundle/Acceptance/acceptance/PageObject/Admin/ProductAdvancedSearchPage.php). They provide a way to reuse code that interacts with user interface.

### Performance tests
These tests assert that key actions do not take too long. They are similar to [HTTP smoke tests]() but they measure response time as well. In addition to routes tested by HTTP smoke tests these tests also request and measure regeneration of all product feeds.

Before execution of the test suite the testing database is filled with large amount of data simulating production environment of a big e-commerce project. You will no longer unknowingly slow down a page because you are developing with only a small data set.

It is advised to run these tests on a separate server that is not under load at the time for consistent results (eg. only in nighttime).

#### Advantages:
* can test performance on large amount of data

#### Disadvantages:
* take really long time to execute (approx. 1.5 hours including import of performance data)
* must be ran on a server without load for consistent results

#### Great for:
* discovering performance impact of code modifications
* preventing application collapse on production data load

#### Example:
See configuration of performance (and [HTTP smoke](#http-smoke-tests)) tests in [`\Tests\ShopBundle\Smoke\Http\UrlsProvider`](../../tests/ShopBundle/Smoke/Http/UrlsProvider.php).

For testing performance of something else than controller actions see implementation of feed performance test in [`\Tests\ShopBundle\Performance\Feed\AllFeedsTest`](../../tests/ShopBundle/Performance/Feed/AllFeedsTest.php).

## How many tests should you write
> The crucial question you should ask yourself is this: do I care about the future of my code?  
> Tests are meant to allow for safe refactoring later on.
>
> \- Matthias Noback in *Principles of package design*

Basically, there is no definite answer to the question of how many tests are enough.
It depends on how much we want to be sure that things will not break in future and how much time are we willing to invest into that.

Be aware that very high test coverage can lead to expensive maintenance that may overweight the benefits.
