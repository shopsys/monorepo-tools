# Upgrade Instructions for Read Model for Product Lists

There is a new layer in Shopsys Framework, called [read model](/docs/model/introduction-to-read-model.md), separating the templates and application model.
Besides better logical separation of the application, it is a first step towards usage of Elasticsearch for frontend product lists, and hence significant performance boost in the near future.
The read model package is marked as *experimental* at the moment so there is a possibility we might introduce some BC breaking changes there.
You do not need to perform the upgrade instantly, however, if you do so, you will be better prepared for the upcoming changes.

There are two implementations of read model facades right now as we want to provide you possibilities that suits you the best.
You can choose between SQL or Elasticsearch implementation, you can learn more in [Front-end product filtering](/docs/model/front-end-product-filtering.md).

<!-- TODO change link to PR to the split merge commit in project-base -->
To start using the read model, follow the instructions (you can also find inspiration in [#1018](https://github.com/shopsys/shopsys/pull/1018) where the read model was introduced to `project-base`):
- add dependency on `shopsys/read-model` to your `composer.json`
- register the bundle in your `app/AppKernel.php`:
    ```php
    // ...
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = [
                // ...
                new Shopsys\ReadModelBundle\ShopsysReadModelBundle(),
                // ...
            ];

            // ...

            return $bundles;
        }

        // ...
    }
    ```
    - Note: the bundle needs to be registered after `ShopsysFrameworkBundle` as it overwrites its `image` Twig function
- in all frontend controllers where you are listing products (`ProductController`, `CartController`, `HomepageController`):
    - add new `$listedProductViewFacade` property
        ```diff
        + /**
        +  * @var \Shopsys\ReadModelBundle\Product\Listed\ListedProductViewFacadeInterface
        +  */
        + private $listedProductViewFacade;
        ```
    - modify the constructors to inject an instance of `Shopsys\ReadModelBundle\Product\Listed\ListedProductViewFacadeInterface` to the property:
        ```diff
        public function __construct(
            // ...
        +    ListedProductViewFacadeInterface $listedProductViewFacade
        ) {
            // ...
        +    $this->listedProductViewFacade = $listedProductViewFacade;
        }
        ```
    - use the `ListedProductViewFacadeInterface` instance to get product views:
        - in `ProductController::detailAction`:
            ```diff
            - $accessories = $this->productOnCurrentDomainFacade->getAccessoriesForProduct($product);
            + $accessories = $this->listedProductViewFacade->getAllAccessories($product->getId());
            ```
        - in `ProductController::listByCategoryAction`:
            ```diff
            - $paginationResult = $this->productOnCurrentDomainFacade->getPaginatedProductsInCategory(
            -      $productFilterData,
            -      $orderingModeId,
            -      $page,
            -      self::PRODUCTS_PER_PAGE,
            -      $id
            -  );
            + $paginationResult = $this->listedProductViewFacade->getFilteredPaginatedInCategory(
            +      $id,
            +      $productFilterData,
            +      $orderingModeId,
            +      $page,
            +      self::PRODUCTS_PER_PAGE
            +  );
            ```
        - in `ProductController::searchAction`:
            ```diff
            - $paginationResult = $this->productOnCurrentDomainFacade->getPaginatedProductsForSearch(
            + $paginationResult = $this->listedProductViewFacade->getFilteredPaginatedForSearch(
            ```
        - in `ProductController::listByBrandAction`:
            ```diff
            - $paginationResult = $this->productOnCurrentDomainFacade->getPaginatedProductsForBrand(
            -     $orderingModeId,
            -     $page,
            -     self::PRODUCTS_PER_PAGE,
            -     $id
            - );
            + $paginationResult = $this->listedProductViewFacade->getPaginatedForBrand(
            +     $id,
            +     $orderingModeId,
            +     $page,
            +     self::PRODUCTS_PER_PAGE
            + );
            ```
        - in `CartController::addProductAjaxAxction`:
            ```diff
            - $accessories = $this->productAccessoryFacade->getTopOfferedAccessories(
            -     $addProductResult->getCartItem()->getProduct(),
            -     $this->domain->getId(),
            -     $this->currentCustomer->getPricingGroup(),
            -     self::AFTER_ADD_WINDOW_ACCESSORIES_LIMIT
            );
            + $accessories = $this->listedProductViewFacade->getAccessories(
            +     $addProductResult->getCartItem()->getProduct()->getId(),
            +     self::AFTER_ADD_WINDOW_ACCESSORIES_LIMIT
            + );
            ```
        - in `HomepageController::indexAction`:
            ```diff
            - $topProducts = $this->topProductFacade->getAllOfferedProducts(
            -     $this->domain->getId(),
            -     $this->currentCustomer->getPricingGroup()
            - );
            + $topProducts = $this->listedProductViewFacade->getAllTop();
            ```
- add new `CartController::productActionAction`:
    ```diff
    // src/Shopsys/ShopBundle/Controller/Front/CartController.php

    + /**
    +  * @param \Shopsys\ReadModelBundle\Product\Action\ProductActionView $productActionView
    +  * @param string $type
    +  */
    + public function productActionAction(ProductActionView $productActionView, $type = 'normal')
    + {
    +     $form = $this->createForm(AddProductFormType::class, ['productId' => $productActionView->getId()], [
    +         'action' => $this->generateUrl('front_cart_add_product'),
    +     ]);

    +     return $this->render('@ShopsysShop/Front/Inline/Cart/productAction.html.twig', [
    +         'form' => $form->createView(),
    +         'productActionView' => $productActionView,
    +         'type' => $type,
    +     ]);
    + }
    ```
- edit your `productListMacro.html.twig` so it now works with instances of `ListedProductView` instead of `Product` entities. You can see the complete diff of the template [here](https://github.com/shopsys/shopsys/pull/1018/files#diff-0f5d7197a48555d8902a9391ea330e6f)
    - in the macro, you now need to render product flags by their ids using `renderFlagsByIds` function:
        - copy `FlagsExtension` class from [here](/project-base/src/Shopsys/ShopBundle/Twig/FlagsExtension.php) and put it to your new `src/Shopsys/ShopBundle/Twig/FlagsExtension.php`
        - copy `twig.yml` services configuration [here](/project-base/src/Shopsys/ShopBundle/Resources/config/services/twig.yml) and put it in your new `src/Shopsys/ShopBundle/Resources/config/services/twig.yml` so the Twig extension is registered as Symfony service
        - copy `productFlags.html.twig` template from [here](/project-base/src/Shopsys/ShopBundle/Resources/views/Front/Inline/Product/productFlags.html.twig) and put it to your new `src/Shopsys/ShopBundle/Resources/views/Front/Inline/Product/productFlags.html.twig`
    - in the macro, to render "add to cart" form, use the new `CartController::productActionAction` instead of `addProductFormAction`
        ```diff
         - {% if not product.isMainVariant %}
         -    {{ render(controller('ShopsysShopBundle:Front/Cart:addProductForm',{
         -        product: product
         -    }

         -    )) }}
        - {% else %}
        -     <a href="{{ url('front_product_detail', { id: product.id }) }}" class="btn btn--success">{{ 'Choose variant'|trans }}</a>
        - {% endif %}
        + {{ render(controller('ShopsysShopBundle:Front/Cart:productAction', { productActionView: productView.action } )) }}
        ```
    - update Elasticsearch structure in `src/Shopsys/ShopBundle/Resources/definition/product/*.json` like this:
        ```diff
        "mappings": {
          "_doc": {
            "properties": {

               //...

              "prices": {
                "type": "nested",
                "properties": {
                  "pricing_group_id": {
                    "type": "integer"
                  },
        -         "amount": {
        +         "price_with_vat": {
                    "type": "float"
        -         }
        +         },
        +         "price_without_vat": {
        +           "type": "float"
        +         },
        +         "vat": {
        +           "type": "float"
        +         },
        +         "price_from": {
        +           "type": "boolean"
        +         }
                }
              },

              //...

        +     "selling_denied": {
        +       "type": "boolean"
        +     },
        +     "availability": {
        +       "type": "text"
        +     },
        +     "main_variant": {
        +       "type": "boolean"
        +     },
        +     "detail_url": {
        +       "type": "text"
        +     },
        +     "visibility": {
        +       "type": "nested",
        +       "properties": {
        +         "pricing_group_id": {
        +           "type": "integer"
        +         },
        +         "visible": {
        +           "type": "boolean"
        +         }
        +       }
        +     }
            }
        ```
    - fix test `ProductSearchExportRepositoryTest::getExpectedStructureForRepository()` by adding new Elasticsearch fields to it
        ```diff
            if ($productSearchExportRepository instanceof ProductSearchExportWithFilterRepository) {
                $structure = \array_merge($structure, [
        +           'availability',
                    'brand',
                    'flags',
                    'categories',
        +           'detail_url',
                    'in_stock',
                    'prices',
                    'parameters',
                    'ordering_priority',
                    'calculated_selling_denied',
        +           'selling_denied',
        +           'main_variant',
        +           'visibility',
                ]);
            }
        ```
    - fix tests in `ListedProductViewFacadeTest` by changing `ListedProductViewFacade` to `ListedProductViewFacadeInterface`
        ```diff
        -   $listedProductViewFacade = $this->getContainer()->get(ListedProductViewFacade::class);
        +   $listedProductViewFacade = $this->getContainer()->get(ListedProductViewFacadeInterface::class);
        ```
    ### Use SQL read model facade
    - use `ListedProductViewFacade` implementation of `ListedProductViewFacadeInterface` in `services.yml` and `services_test.yml`
        ```yaml
            Shopsys\ReadModelBundle\Product\Listed\ListedProductViewFacadeInterface: '@Shopsys\ReadModelBundle\Product\Listed\ListedProductViewFacade'
        ```
    - to use this implementation you need to use `ProductOnCurrentDomainFacade` as well. You can find more about it [Front-end product filtering](/docs/model/front-end-product-filtering.md)
    ### Use Elasticsearch read model facade
    - use `ListedProductViewElasticFacade` implementation of `ListedProductViewFacadeInterface` in `services.yml` and `services_test.yml`
        ```yaml
            Shopsys\ReadModelBundle\Product\Listed\ListedProductViewFacadeInterface: '@Shopsys\ReadModelBundle\Product\Listed\ListedProductViewElasticFacade'

            Shopsys\ReadModelBundle\Product\Listed\ListedProductViewElasticFacade: ~  
        ```
     - to use this implementation you need to use `ProductOnCurrentDomainElasticFacade` as well. You can find more about it [Front-end product filtering](/docs/model/front-end-product-filtering.md)
