# Shopsys HTTP Smoke Testing

[![Build Status](https://travis-ci.org/shopsys/http-smoke-testing.svg?branch=master)](https://travis-ci.org/shopsys/http-smoke-testing)
[![Downloads](https://img.shields.io/packagist/dt/shopsys/http-smoke-testing.svg)](https://packagist.org/packages/shopsys/http-smoke-testing)

This package enables you to do simple HTTP smoke testing of your Symfony application.

Basically, it generates a HTTP request for every page (controller action) provided by the application router and then asserts that the returned HTTP response code is correct.

While this is not a very sophisticated check, it can answer the essential question *"does it run?"*.
It prevents you from triggering *500 Server Error* on some seemingly unrelated page when you are doing changes in shared code.
Moreover, after initial configuration it is almost maintenance-free as it checks any new routes automatically.

## Installation
Add the package to `require-dev` in your application:
```
composer require --dev shopsys/http-smoke-testing
```

This package internally uses [PHPUnit](https://phpunit.de/) to run the tests.
That means that you need to setup your `phpunit.xml` properly.
Fortunately, Symfony comes with example configuration.
Renaming the `phpunit.xml.dist` in your project root (or `app/phpunit.xml.dist` on Symfony 2) should be sufficient.

*Note: If you did not find the file in your project check out the example in [Symfony Standard Edition](https://github.com/symfony/symfony-standard).*

## Usage
Create [new PHPUnit test](https://phpunit.de/manual/current/en/writing-tests-for-phpunit.html) extending [`\Shopsys\HttpSmokeTesting\HttpSmokeTestCase`](./src/HttpSmokeTestCase.php) class and implement `customizeRouteConfigs` method.

You can run your new test by:

```
php vendor/bin/phpunit tests/AppBundle/Smoke/SmokeTest.php
```

(or `php bin/phpunit -c app/phpunit.xml src/AppBundle/Tests/Smoke/SmokeTest.php` on Symfony 2)

**Warning: This package checks all routes by making real requests.**
**It is important not to execute it on production data.**
**You may unknowingly delete or modify your data or real requests on 3rd party services.**
Even if you implement some way of protecting the application from side-effect (eg. database transaction wrapping) you should never execute tests on production data.

### Example test class
```php
<?php

namespace Tests\AppBundle\Smoke;

use Shopsys\HttpSmokeTesting\Auth\BasicHttpAuth;
use Shopsys\HttpSmokeTesting\HttpSmokeTestCase;
use Shopsys\HttpSmokeTesting\RouteConfig;
use Shopsys\HttpSmokeTesting\RouteConfigCustomizer;
use Shopsys\HttpSmokeTesting\RouteInfo;
use Symfony\Component\HttpFoundation\Request;

class SmokeTest extends HttpSmokeTestCase {
    /**
     * @param \Shopsys\HttpSmokeTesting\RouteConfigCustomizer $routeConfigCustomizer
     */
    protected function customizeRouteConfigs(RouteConfigCustomizer $routeConfigCustomizer)
    {
        $routeConfigCustomizer
            ->customize(function (RouteConfig $config, RouteInfo $info) {
                // This function will be called on every RouteConfig provided by RouterAdapter
                if ($info->getRouteName()[0] === '_') {
                    // You can use RouteConfig to change expected behavior or skip testing particular routes
                    $config->skipRoute('Route name is prefixed with "_" meaning internal route.');
                }
            })
            ->customizeByRouteName('acme_demo_secured_hello', function (RouteConfig $config, RouteInfo $info) {
                // You can customize RouteConfig to use authentication for secured routes
                $config->changeDefaultRequestDataSet('Log in as "user".')
                    ->setAuth(new BasicHttpAuth('user', 'userpass'));
            });
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleRequest(Request $request)
    {
        $entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        // Enclose request handling in rolled-back database transaction to prevent side-effects
        $entityManager->beginTransaction();
        $response = parent::handleRequest($request);
        $entityManager->rollback();

        return $response;
    }
}
```

## Documentation
By default the test makes request to every route without using any authentication or providing any parameters and expects the response to have HTTP status code *200 OK*.

To change this behavior you must implement method `customizeRouteConfigs(RouteConfigCustomizer $routeConfigCustomizer)` in your test.

[`RouteConfigCustomizer`](./src/RouteConfigCustomizer.php) provides two methods for customizing individual route requests:
* `customize` accepts callback `function (RouteConfig $config, RouteInfo $info) {...}` as the only argument.
This is called with each [`RouteConfig`](./src/RouteConfig.php) along with [`RouteInfo`](./src/RouteInfo.php) collected from your router.  
This method is useful when you want to define general rules for multiple routes (eg. skip all routes with name starting with underscore).
* `customizeByRouteName` accepts a single route name or an array of route names as the first argument and same callback as `customize` as the second argument.
This is called with each [`RouteConfig`](./src/RouteConfig.php) along with [`RouteInfo`](./src/RouteInfo.php) with matching route name.
If matching route config is not found a [`RouteNameNotFoundException`](./src/Exception/RouteNameNotFoundException.php) is thrown.  
This method is useful when you want to define rules for specific routes (eg. logging in to some secured route).

In your customizing callback you can call three methods on [`RouteConfig`](./src/RouteConfig.php) to change the tested behavior:
* `skipRoute` can be called to skip this route during test.
* `changeDefaultRequestDataSet` is the main method for configuring routes.
It returns [`RequestDataSet`](./src/RequestDataSet.php) object offering the setters needed to change the actual behavior:
  * `setExpectedStatusCode` changes the expected response HTTP status code that will be asserted.
  * `setAuth` changes the authentication method for the route.
  (Use [`NoAuth`](./src/Auth/NoAuth.php) for anonymous access, [`BasicHttpAuth`](./src/Auth/BasicHttpAuth.php) for logging in via basic http headers
  or implement your own method using [`AuthInterface`](./src/Auth/AuthInterface.php).)
  * `setParameter` specifies value of a route parameter by name.
  * `addCallDuringTestExecution` adds a callback `function (RequestDataSet $requestDataSet, ContainerInterface $container) { ... }` to be called before test execution.  
  (Useful for code that needs to access the same instance of container as the test method, eg. adding CSRF token as a route parameter)
* `addExtraRequestDataSet` can be used to test more requests on the same route (eg. test a secured route as both logged in and anonymous user).
Returns [`RequestDataSet`](./src/RequestDataSet.php) that you can use the same way as the result from `changeDefaultRequestDataSet`.
All configured options will extend the values from default request data set (even when you change the default [`RequestDataSet`](./src/RequestDataSet.php) after you add the extra [`RequestDataSet`](./src/RequestDataSet.php)).

*Note: All three methods of [`RouteConfigCustomizer`](./src/RouteConfigCustomizer.php) accept `string $debugNote` as an argument.*
*It is useful for describing the reasons of your configuration change because it may help you with debugging when the test fails.*

Additionally you can override these methods in your implementation of [`HttpSmokeTestCase`](./src/HttpSmokeTestCase.php) to further change the test behavior:
* `setUp` to change the way your kernel is booted (eg. boot it with different options).
* `getRouterAdapter` to change the object responsible for collecting routes from your application and generating urls.
* `createRequest` if you have specific needs about the way `Request` is created from [`RequestDataSet`](./src/RequestDataSet.php).
* `handleRequest` to customize handling `Request` in your application (eg. you can wrap it in database transaction to roll it back into original state).

## Troubleshooting

### Tests do not fail on non-existing route
PHPUnit by default does not fail on warnings. Setting `failOnWarning="true"` in `phpunit.xml` fixes this problem.

## Contributing
Thank you for your contributions to Shopsys HTTP Smoke Testing package.
Together we are making Shopsys Framework better.

This repository is READ-ONLY.
If you want to [report issues](https://github.com/shopsys/shopsys/issues/new) and/or send [pull requests](https://github.com/shopsys/shopsys/compare),
please use the main [Shopsys repository](https://github.com/shopsys/shopsys).

Please, check our [Contribution Guide](https://github.com/shopsys/shopsys/blob/master/CONTRIBUTING.md) before contributing.

## Support
What to do when you are in troubles or need some help? Best way is to contact us on our Slack [http://slack.shopsys-framework.com/](http://slack.shopsys-framework.com/)

If you want to [report issues](https://github.com/shopsys/shopsys/issues/new), please use the main [Shopsys repository](https://github.com/shopsys/shopsys).

