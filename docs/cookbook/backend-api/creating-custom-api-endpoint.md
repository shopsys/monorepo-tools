# Creating Custom API Endpoint

This cookbook provides you all the necessary steps that you will need for extending your [backend API](/docs/backend-api/introduction-to-backend-api.md) with a custom endpoint.

To keep the cookbook as simple as possible, we will just add `/hello/<name>` endpoint that will greet the API client.

## 1. Add a new controller that extends `AbstractFOSRestController`
```php
declare(strict_types=1);

namespace Shopsys\ShopBundle\Controller\Api\V1\Product;

use FOS\RestBundle\Controller\AbstractFOSRestController;

class HelloController extends AbstractFOSRestController
{
}
```
## 2. In the controller, add `helloAction()` method
We will mark the action as "GET API method" using `FOS\RestBundle\Controller\Annotations\Get` annotation.
Then, we need to create a view object and return a response object, like in any other action in standard [Symfony controller](https://symfony.com/doc/3.4/controller.html).

```diff
declare(strict_types=1);

namespace Shopsys\ShopBundle\Controller\Api\V1\Product;

use FOS\RestBundle\Controller\AbstractFOSRestController;
+ use FOS\RestBundle\Controller\Annotations\Get;
+ use FOS\RestBundle\View\View;
+ use Symfony\Component\HttpFoundation\Response;

class HelloController extends AbstractFOSRestController
{
+     /**
+      * @Get("/hello")
+      * @return \Symfony\Component\HttpFoundation\Response
+      */
+     public function helloAction(): Response
+     {
+          $view = View::create(['Hello world!'], Response::HTTP_OK);
+
+          return $this->handleView($view);
+     }
}
```

## 3. Add a parameter for the endpoint
In this step, we will require the client's name as a parameter and reflect it in the response.

```diff
/**
-  * @Get("/hello")
+  * @Get("/hello/{name}")
+  * @param string $name
 * @return \Symfony\Component\HttpFoundation\Response
 */
- public function helloAction(): Response
+ public function helloAction(string $name): Response
{
-     $view = View::create(['Hello world!'], Response::HTTP_OK);
+     $view = View::create([sprintf('Hello %s!', $name)], Response::HTTP_OK);

    return $this->handleView($view);
}
```

## Conclusion
Your new backend API endpoint is now available on the url `<your_domain_url>/api/v1/hello/<name>`. If you are not sure how to test it, you can get inspired in [Introduction to Backend API](/docs/backend-api/introduction-to-backend-api.md#try-it).

The routing for your new endpoint works out of the box, thanks to the `shopsys_shop_api` setting in your [`Resources/config/routing.yml`](/project-base/src/Shopsys/ShopBundle/Resources/config/routing.yml) configuration.

[API Authentication - OAuth2](/docs/backend-api/api-authentication-oauth2.md) is required by default for the endpoint, thanks to the `oauth2` setting in your [`app/config/routing.yml`](/project-base/app/config/routing.yml) configuration.
