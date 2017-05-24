# Adding new page with recently bought products

This cookbook will guide you though creating a new page in Shopsys Framework listing products that were recently bought by the user.
Because this page is useful only for returning customers with history of purchases it will be available for registered users only.
All texts will be fully translated to another language.

This functionality is typical for online stores selling consumables (e.g. pet food, diapers, ...) where big part of the revenue comes from repeated orders.

After completing this cookbook you should be able to:
- create a new page
- define static routes
- access data of currently logged customer
- add new functionality to the model
- list products on page using Twig macro
- add new translations of static texts

## Creating a blank page
First step will be to add a new blank page to the front-end of your store.

### Controller action
Every page has its own controller action.
Controller responsible for offering the user with products is [`\Shopsys\ShopBundle\Controller\Front\ProductController`](../../src/Shopsys/ShopBundle/Controller/Front/ProductController.php).
Add a new method `recentlyBoughtAction` to it.
This will be the action for rendering the list of current user's recently bought products.
At the moment it will just render a new template `@ShopsysShop/Front/Content/Product/recentlyBought.html.twig`.
The logic will be added later.

```php
<?php

class ProductController extends FrontBaseController
{
    // ...

    public function recentlyBoughtAction()
    {
        return $this->render('@ShopsysShop/Front/Content/Product/recentlyBought.html.twig');
    }
}
```

### Template
Create `recentlyBought.html.twig` template in [`src/Shopsys/ShopBundle/Resources/views/Front/Content/Product/`](../../src/Shopsys/ShopBundle/Resources/views/Front/Content/Product/).
By extending the template [`@ShopsysShop/Front/Layout/layoutWithPanel.html.twig`](../../src/Shopsys/ShopBundle/Resources/views/Front/Layout/layoutWithPanel.html.twig) you will get the same layout that other product lists use.

The content of `{% block title %}` will be pasted into the `<title>` tag of the resulting HTML page.
The `{% block main_content %}` should contain the actual content of your new page.
Simple static heading should be enough at the moment as there are no data to display yet.

To allow for future translation into other languages wrap all static texts in `|trans` Twig filter.

```twig
{% extends '@ShopsysShop/Front/Layout/layoutWithPanel.html.twig' %}

{% block title %}{{ 'Recently bought products'|trans }}{% endblock %}

{% block main_content %}
    <h1>{{ 'Your recently bought products'|trans }}</h1>

    <p>{{ 'You have not bought any products in recent history.'|trans }}</p>
{% endblock %}
```

### Route
For the page to be reachable it needs to have a route.
The page should be fully localized, including the URL.
Front-end routes that are dependant on current locale are configured in `routing_front_{locale}.yml` files.

Add a new route `front_product_recently_bought` in [`src/Shopsys/ShopBundle/Resources/config/routing_front_en.yml`](../../src/Shopsys/ShopBundle/Resources/config/routing_front_en.yml) in English.

```yaml
# ...

front_product_recently_bought:
  path: /recently-bought/
  defaults: { _controller: ShopsysShopBundle:Front\Product:recentlyBought }

# ...
```

And add a Czech configuration of the route in [`src/Shopsys/ShopBundle/Resources/config/routing_front_cs.yml`](../../src/Shopsys/ShopBundle/Resources/config/routing_front_cs.yml).

```yaml
# ...

front_product_recently_bought:
  path: /nedavno-nakoupeno/
  defaults: { _controller: ShopsysShopBundle:Front\Product:recentlyBought }

# ...
```

### Breadcrumb navigation
If you try to visit your newly configured page on [http://127.0.0.1:8000/recently-bought/](http://127.0.0.1:8000/recently-bought/) an exception *Breadcrumb generator not found for route "front_product_recently_bought"* will be thrown.
This is because breadcrumb navigation is mandatory in [`layoutWithPanel.html.twig`](../../src/Shopsys/ShopBundle/Resources/views/Front/Layout/layoutWithPanel.html.twig) and it has not been specified how to create breadcrumb navigation for this page yet.

Classes implementing the [`BreadcrumbGeneratorInterface`](../../src/Shopsys/ShopBundle/Component/Breadcrumb/BreadcrumbGeneratorInterface.php) are responsible for generating the breadcrumb items for each route.
The simplest implementation, having only one static breadcrumb item for each route, is [`\Shopsys\ShopBundle\Model\Breadcrumb\SimpleBreadcrumbGenerator`](../../src/Shopsys/ShopBundle/Model/Breadcrumb/SimpleBreadcrumbGenerator.php).
You can add a new item to `routeNameMap` with the route name `front_product_recently_bought` as a key and the desired label as a value.

To allow for future translation into other languages wrap the text in `t()` function.
The function works similarly as `|trans` in Twig.

```php
<?php

class SimpleBreadcrumbGenerator implements BreadcrumbGeneratorInterface
{
    // ...

    /**
     * @return string[]
     */
    private function getRouteNameMap()
    {
        if ($this->routeNameMap === null) {
            $this->routeNameMap = [

                // ...

                'front_product_recently_bought' => t('Recently bought products'),
            ];
        }

        return $this->routeNameMap;
    }
}
```

### Adding link to the new page
Your new page is already accessible on [http://127.0.0.1:8000/recently-bought/](http://127.0.0.1:8000/recently-bought/) but there are no links to this page yet.

As the end functionality is meant to be used by registered users the right place for the link is the user menu in the header.
To see the menu you can log in on front-end of your store as demo customer (e.g. `no-reply@netdevelo.cz` with password `user123`).

To add an item to this menu edit the header template [`src/Shopsys/ShopBundle/Resources/views/Front/Layout/header.html.twig`](../../src/Shopsys/ShopBundle/Resources/views/Front/Layout/header.html.twig) and add new `.menu-iconic__sub__item` element.
Use `url()` Twig function to get the URL for the link by route name.

```twig
{% block header %}
    <header class="header">

        {# ... #}

        <div class="header__top">

            {# ... #}

            <div class="header__top__right">
                <ul class="menu-iconic">
                    {% if is_granted('ROLE_CUSTOMER') %}
                        <li class="menu-iconic__item">

                            {# ... #}

                            <ul class="menu-iconic__sub">

                                {# ... #}

                                <li class="menu-iconic__sub__item">
                                    <a href="{{ url('front_product_recently_bought') }}" class="menu-iconic__sub__item__link">
                                        {{- 'Recently bought'|trans -}}
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {# ... #}
```

## Listing of products
When you have your blank page prepared you can work on displaying the right products on it.

### Repository method
Repositories are generally responsible for fetching the model data.
More specifically, [`\Shopsys\ShopBundle\Model\Product\ProductRepository`](../../src/Shopsys/ShopBundle/Model/Product/ProductRepository.php) is responsible for fetching products.

Add a new method `getRecentlyBoughtProducts` that will return an array of [`Product`](../../src/Shopsys/ShopBundle/Model/Product/Product.php) entities by provided [`User`](../../src/Shopsys/ShopBundle/Model/Customer/User.php) and `DateTime`.

Repositories use query builders for specifying the data to be fetched.
Use the `getAllListableQueryBuilder` method to apply all the rules for listable products (e.g. not hidden and without selling denied).

```php
<?php

// added uses:
use DateTime;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Order\Item\OrderProduct;

class ProductRepository
{
    // ...

    /**
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
     * @param \DateTime $orderCreatedFrom
     * @return \Shopsys\ShopBundle\Model\Product\Product[]
     */
    public function getRecentlyBoughtProducts(User $user, DateTime $orderCreatedFrom)
    {
        return $this->getAllListableQueryBuilder($user->getDomainId(), $user->getPricingGroup())
            ->join(OrderProduct::class, 'op', Join::WITH, 'op.product = p')
            ->join('op.order', 'o')
            ->andWhere('o.customer = :user')->setParameter('user', $user)
            ->andWhere('o.createdAt > :orderCreatedFrom')->setParameter('orderCreatedFrom', $orderCreatedFrom)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
```

In order to get recently bought products you need to join [`Order`](../../src/Shopsys/ShopBundle/Model/Order/Order.php) and [`OrderProduct`](../../src/Shopsys/ShopBundle/Model/Order/Item/OrderProduct.php) relations.

As a general rule, entities are aliased by class name initials in query builders.
Condition `op.product = p` means that joined [`OrderProduct`](../../src/Shopsys/ShopBundle/Model/Order/Item/OrderProduct.php) has to have the [`Product`](../../src/Shopsys/ShopBundle/Model/Product/Product.php) from the original query builder in its `$product` property.

Then you can filter the joined relations by [`User`](../../src/Shopsys/ShopBundle/Model/Customer/User.php) that created the [`Order`](../../src/Shopsys/ShopBundle/Model/Order/Order.php) and time of its creation.

It is always a good idea to define the order in which data should be returned.
By default products fetched using the `getAllListableQueryBuilder` method are ordered by id.
It is probably best to order the products chronologically so that those bought most recently will be on top.

*Note: For more information about the query builder usage consult [Doctrine's QueryBuilder documentation](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/query-builder.html).*

### Facade method
Facades are classes that represent a single point of access to the model.
Controllers should not access the repositories directly, they should access them via facade methods only.
This means you need to implement a new facade method that will be user from your controller.

The easiest way to fetch products for the currently logged [`user`](../../src/Shopsys/ShopBundle/Model/Customer/User.php) on the current [`domain`](../../src/Shopsys/ShopBundle/Component/Domain/Config/DomainConfig.php) is via [`\Shopsys\ShopBundle\Model\Product\ProductOnCurrentDomainFacade`](../../src/Shopsys/ShopBundle/Model/Product/ProductOnCurrentDomainFacade.php).
You should add your new method there.
The facade method will be responsible for providing the repository with current [`User`](../../src/Shopsys/ShopBundle/Model/Customer/User.php) and correct time limit for recent orders.

Also it will enrich the [`Product`](../../src/Shopsys/ShopBundle/Model/Product/Product.php) entities with additional info important for displaying on front-end (e.g. calculated selling price, parameters, images) by mapping them to instances of [`ProductDetail`](../../src/Shopsys/ShopBundle/Model/Product/Detail/ProductDetail.php).

```php
<?php

// added use
use DateTime;

class ProductOnCurrentDomainFacade
{
    // ...

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Detail\ProductDetail[]
     */
    public function getRecentlyBoughtProductDetails()
    {
        $user = $this->currentCustomer->findCurrentUser();
        if ($user === null) {
            return [];
        }

        $orderCreatedFrom = new DateTime('-3 months');
        $products = $this->productRepository->getRecentlyBoughtProducts($user, $orderCreatedFrom);

        return $this->productDetailFactory->getDetailsForProducts($products);
    }
}
```

Notice that method `findCurrentUser` of class [`CurrentCustomer`](../../src/Shopsys/ShopBundle/Model/Customer/CurrentCustomer.php) returns `null` if no [`User`](../../src/Shopsys/ShopBundle/Model/Customer/User.php) is currently logged in.
This `null` cannot be passed into [`ProductRepository`](../../src/Shopsys/ShopBundle/Model/Product/ProductRepository.php) as it would trigger `TypeError`.
As there are no products to be fetched the method can return an empty array.

*Note: Getter methods that may return `null` are always named `findSomething` in repositories and facades.*
*When the method never returns `null` it is named `getSomething` (they typically return either an entity of giver type or an array of entities) or `isSomething` / `hasSomething` if it returns `bool`.*

### Controller usage
[`ProductController`](../../src/Shopsys/ShopBundle/Controller/Front/ProductController.php) already has the [`ProductOnCurrentDomainFacade`](../../src/Shopsys/ShopBundle/Model/Product/ProductOnCurrentDomainFacade.php) as a dependency so it is ready to use.

You can just fetch the products in your `recentlyBoughtAction` method and pass them into your template to be listed.

*Tip: To be sure everything works as expected you can try dumping the variable by calling `d($productDetails)`.*
*Contents of the variable will appear in the debug toolbar (right next to the database queries section).*

```php
<?php

class ProductController extends FrontBaseController
{
    // ...

    public function recentlyBoughtAction()
    {
        $productDetails = $this->productOnCurrentDomainFacade->getRecentlyBoughtProductDetails();

        return $this->render('@ShopsysShop/Front/Content/Product/recentlyBought.html.twig', [
            'productDetails' => $productDetails,
        ]);
    }
}
```

### Template usage
You can display the products in `recentlyBought.html.twig` template using an existing Twig macro.

To be able to use it you have to import [`@ShopsysShop/Front/Content/Product/productListMacro.html.twig`](../../src/Shopsys/ShopBundle/Resources/views/Front/Content/Product/productListMacro.html.twig) first.
Aliasing it allows for shorter usage of the macro `list` by calling `{{ productList.list(productDetails) }}`.

Macros let you reuse parts of template for consistent look and ease of development.

```twig
{% extends '@ShopsysShop/Front/Layout/layoutWithPanel.html.twig' %}
{% import '@ShopsysShop/Front/Content/Product/productListMacro.html.twig' as productList %}

{% block title %}{{ 'Recently bought products'|trans }}{% endblock %}

{% block main_content %}
    <h1>{{ 'Your recently bought products'|trans }}</h1>

    {% if productDetails is not empty %}
        {{ productList.list(productDetails) }}
    {% else %}
        <p>{{ 'You have not bought any products in recent history.'|trans }}</p>
    {% endif %}
{% endblock %}
```

## Adding translations
In the last chapter you will see how translation of static texts works in Shopsys Framework.

### Extraction of translated messages
[Phing target `dump-translations`](../introduction/phing-targets.md#dump-translations) can help you by extraction of all translatable messages.

```
php phing dump-translations
```

This command will add new translatable messages to `*.po` files in [`src/Shopsys/ShopBundle/Resources/translations/`](../../src/Shopsys/ShopBundle/Resources/translations/).

### Adding your translations
You can leave the [`messages.en.po`](../../src/Shopsys/ShopBundle/Resources/translations/messages.en.po) unchanged as the source messages are already in English.

In files with translations to other languages (i.e. [`messages.cs.po`](../../src/Shopsys/ShopBundle/Resources/translations/messages.cs.po)) you have to provide desired translations of all messages without filled translation (having `msgstr ""`).

Extracted files contain all files in which the message was found.
This information can help you with translation by providing you with context.

```yaml
# ...

#: src/Shopsys/ShopBundle/Resources/views/Front/Layout/header.html.twig:81
msgid "Recently bought"
msgstr "Nedávno nakoupeno"

#: SimpleBreadcrumbGenerator.php:54
#: src/Shopsys/ShopBundle/Resources/views/Front/Content/Product/recentlyBought.html.twig:4
msgid "Recently bought products"
msgstr "Nedávno nakoupené zboží"

# ...

#: src/Shopsys/ShopBundle/Resources/views/Front/Content/Product/recentlyBought.html.twig:12
msgid "You have not bought any products in recent history."
msgstr "V nedávné minulosti jste žádný produkt nekoupili."

# ...

#: src/Shopsys/ShopBundle/Resources/views/Front/Content/Product/recentlyBought.html.twig:7
msgid "Your recently bought products"
msgstr "Vaše nedávno nakoupené zboží"

# ...
```

After providing the translations you should be able to see you new translated page on all configured domains.
You should be able to see it on [http://127.0.0.1:8001/nedavno-nakoupeno/](http://127.0.0.1:8001/nedavno-nakoupeno/) in Czech language.

## Conclusion
Now you know how to add new localized custom pages to your online store and how to create links to these pages using routes.

You have also learned how to fetch products from the database by your own criteria via Doctrine's QueryBuilder and list them on the front-end using Twig macros.

By creating new methods in both facades and repositories you now better understand the architecture of the model so you are able to make your code more reusable.
