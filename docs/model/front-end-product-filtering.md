# Front-end Product Filtering
Products can be by default filtered by price, flags, brand, parameters and in stock availability.

Filtering can be performed on category list and search results.  
These two pages are represented by `ProductController` and `SearchController`, where is used the interface (`Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainFacadeInterface`) that describes common methods to get a filtered result:
 - `getPaginatedProductsInCategory()` to obtain filtered products in category
 - `getPaginatedProductsForSearch()` to obtain filtered products from search results

Currently, there are two implementations of `ProductOnCurrentDomainFacadeInterface`:
 - `ProductOnCurrentDomainElasticFacade` *(default)*
    - filters data through Elasticsearch
    - much faster than filtering through SQL and remains fast independently on the number of selected filters
 - `ProductOnCurrentDomainFacade`
    - filters data through SQL
    - slower than Elasticsearch, but on the other hand can be used easily on more complex pricing models (for example exact price is calculated with SQL function)

## Filtering through Elasticsearch
Behavior of the filter is defined in the class `Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainElasticFacade`.

Each filtering method internally uses their own factory method `createProducts*FilterQuery` to create `Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery` object that represents the query for Elasticsearch.

Elasticsearch return a sorted list of product IDs and products itself are loaded from PostgreSQL.

Aggregation numbers are counted with help of Elasticsearch too thanks to methods `Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainElasticFacade::getProductFilterCountDataInCategory` and
`Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainElasticFacade::getProductFilterCountDataForSearch`.

List of choices (exact parameters, brands, flags) is loaded from PostgreSQL as there is no benefit from loading them from Elasticsearch.

## Filtering through SQL
Behavior of the filter is defined in the class `Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainFacade`.

Each filtering method calls appropriate method in `Shopsys\FrameworkBundle\Model\Product\ProductRepository` class in which a Doctrine `QueryBuilder` object is composed to get proper products with SQL query.

## Choose an Implementation
You can choose which one of them will be used by setting one of the previously mentioned implementations in your `services.yml` file:
```yaml
    # Elasticsearch filtering
    Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainFacadeInterface: '@Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainElasticFacade'
```
or
```yaml
    # SQL filtering
    Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainFacadeInterface: '@Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainFacade'
```

*Note: If you need to extend the implementation of your choice, it is possible you will need to adjust abstract test `Tests\ShopBundle\Functional\Model\Product\ProductOnCurrentDomainFacadeTest` accordingly.
In that case is perfectly fine to skip or delete implementation of this test for the one you don't use.*
