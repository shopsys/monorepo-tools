# Adding Ajax Load More Button into Pagination

In this cookbook, we will add a paginated brand list including ajax "load more" button to a product list page. After finishing the guide, you will know how to use multiple paginations on one page.  

*Note: After this change you will have paginated also `/brands-list/` page (`front_brand_list` route).*

## Implementation of Brand Pagination

First we need to extend `Shopsys\FrameworkBundle\Model\Product\Brand\BrandRepository` by creating `BrandRepository.php` in `/src/Shopsys/ShopBundle/Model/Product/Brand` and we add `getPaginationResult` method.
```php
namespace Shopsys\ShopBundle\Model\Product\Brand;

use Shopsys\FrameworkBundle\Component\Paginator\QueryPaginator;
use Shopsys\FrameworkBundle\Model\Product\Brand\BrandRepository as BaseBrandRepository;

class BrandRepository extends BaseBrandRepository
{
    /**
     * @param int $page
     * @param int $limit
     * @return \Shopsys\FrameworkBundle\Component\Paginator\PaginationResult
     */
    public function getPaginationResult(
        $page,
        $limit
    ) {
        $queryBuilder = $this->getBrandRepository()->createQueryBuilder('b');
        $queryBuilder->orderBy('b.name', 'asc');

        /** @var \Shopsys\FrameworkBundle\Component\Paginator\QueryPaginator $queryPaginator */
        $queryPaginator = new QueryPaginator($queryBuilder);

        return $queryPaginator->getResult($page, $limit);
    }
}
```

Then we will extend `Shopsys\FrameworkBundle\Model\Product\Brand\BrandFacade` and add `getPaginatedResult` method.
```php
namespace Shopsys\ShopBundle\Model\Product\Brand;

use Shopsys\FrameworkBundle\Component\Paginator\PaginationResult;
use Shopsys\FrameworkBundle\Model\Product\Brand\BrandFacade as BaseBrandFacade;

class BrandFacade extends BaseBrandFacade
{
    /**
     * @param int $page
     * @param int $limit
     * @return \Shopsys\FrameworkBundle\Component\Paginator\PaginationResult
     */
    public function getPaginatedResult(
        $page,
        $limit
    ) {
        $paginationResult = $this->brandRepository->getPaginationResult(
            $page,
            $limit
        );

        return new PaginationResult(
            $paginationResult->getPage(),
            $paginationResult->getPageSize(),
            $paginationResult->getTotalCount(),
            $paginationResult->getResults()
        );
    }
}
```

After we have extended `BrandRepository` and `BrandFacade` we need to set them to be used instead of the framework classes in our application. This is done via configuration in `services.yml`.
```yaml
    Shopsys\FrameworkBundle\Model\Product\Brand\BrandRepository: '@Shopsys\ShopBundle\Model\Product\Brand\BrandRepository'

    Shopsys\FrameworkBundle\Model\Product\Brand\BrandFacade: '@Shopsys\ShopBundle\Model\Product\Brand\BrandFacade'
```

Next we will modify the brand list twig template where we replace the whole content and create twig template for rendering paging controls and paginated items via ajax where we move the original content from brand list template.  
We will use `paginator.loadMoreButton(paginationResult, url('front_brand_list'), pageQueryParameter)` twig component that will asynchronously load next page when user clicks on its button. We will also define `pageQueryParameter` variable so it will have unique name and it will not interfere with other paging component on the same page.

```twig
{# ShopBundle/Resources/views/Front/Content/Brand/list.html.twig #}
{% extends '@ShopsysShop/Front/Layout/layoutWithPanel.html.twig' %}

{% block title %}
    {{ 'Brand overview'|trans }}
{% endblock %}

{% block main_content %}
    <div>
        <h1>{{ 'Brand overview'|trans }}</h1>
        {% include '@ShopsysShop/Front/Content/Brand/ajaxList.html.twig' with {paginationResult: paginationResult} %}
    </div>
{% endblock %}
```

There are two important css classes that must be used.
- `js-list-with-paginator` - element with this class encapsulate paging component
- `js-list` - fragment from which new items are pulled during asynchronous call

```twig
{# ShopBundle/Resources/views/Front/Content/Brand/ajaxList.html.twig #}
{% import '@ShopsysShop/Front/Inline/Paginator/paginator.html.twig' as paginator %}
{% set entityName = 'brands'|trans %}
{% set pageQueryParameter = 'brandPage' %}

<div>
    <div class="js-list-with-paginator">
        {{ paginator.paginatorNavigation(paginationResult, entityName, pageQueryParameter) }}
        <ul class='list-images js-list'>
            {% for brand in paginationResult.results %}
                <li class="list-images__item">
                    <a href="{{ url('front_brand_detail', { id: brand.id }) }}" class="list-images__item__block list-images__item__block--with-label">
                        {{ image(brand, { alt: brand.name }) }}
                        <span>{{ brand.name }}</span>
                    </a>
                </li>
            {% endfor %}
        </ul>
        <div class="text-center margin-bottom-20">
            {{ paginator.loadMoreButton(paginationResult, url('front_brand_list'), pageQueryParameter) }}
        </div>
        {{ paginator.paginatorNavigation(paginationResult, entityName, pageQueryParameter) }}
    </div>
</div>
```

After that we will modify `listAction` method in `BrandController` so `Brand` list page will be paginated and we will be able to integrate it into another list page that has other paginated items.  
We will implement also constants for page query parameter `const PAGE_QUERY_PARAMETER = 'brandPage'` and for the count of items on one page `const ITEMS_PER_PAGE = 5;`.

To be able to determine whether the request for brand list is called from the template, we need to add dependecy on `Symfony\Component\HttpFoundation\RequestStack` into our `BrandController`.

```php

const PAGE_QUERY_PARAMETER = 'brandPage';
const ITEMS_PER_PAGE = 5;

/**
 * @var \Symfony\Component\HttpFoundation\RequestStack
 */
private $requestStack;

/**
 * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandFacade $brandFacade
 * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
 */
public function __construct(
    BrandFacade $brandFacade,
    RequestStack $requestStack
) {
    $this->brandFacade = $brandFacade;
    $this->requestStack = $requestStack;
}


/**
 * @param \Symfony\Component\HttpFoundation\Request $request
 */
public function listAction(Request $request)
{
    // check whether request is called directly via route or via Twig template
    $isMasterRequest = $this->requestStack->getMasterRequest() === $request;

    if ($request->isXmlHttpRequest() || !$isMasterRequest) {
        $template = '@ShopsysShop/Front/Content/Brand/ajaxList.html.twig';
    } else {
        $template = '@ShopsysShop/Front/Content/Brand/list.html.twig';
    }

    $requestPage = $request->get(self::PAGE_QUERY_PARAMETER);
    $page = $requestPage === null ? 1 : (int)$requestPage;

    return $this->render($template, [
        'paginationResult' => $this->brandFacade->getPaginatedResult($page, self::ITEMS_PER_PAGE),
    ]);
}
```

### Customizing the "load more" button text
By default, the "load more" button displays general text - "Load next X item(s)".
There is an option `buttonTextCallback` available for `Shopsys.AjaxMoreLoader` javascript component that you can use to customize the displayed text to fit your use case.
You can see the usage of the option in [`productList.js`](/project-base/src/Shopsys/ShopBundle/Resources/scripts/frontend/product/productList.js).

## Integration of Paginated Brand List

Now we have working implementation of paginated `Brand` list page that can be loaded also from asynchronous calls.
We can try to integrate it into another `Product` list page that is also paginated with page query parameter `page`.
Only thing we need to do is to modify template for `Product` page.  
We will add twig code into `main_content` block.

```twig
{# ShopBundle/Resources/views/Front/Content/Product/list.html.twig #}
{{ render(controller('ShopsysShopBundle:Front/Brand:list')) }}
```

## Conclusion

Customer can see 2 paginated lists with buttons for loading items from next pages on each `Product` list page. Since there are unique page query parameters, paginated lists can have displayed different page indexes after browser is loaded with these page query parameters.
