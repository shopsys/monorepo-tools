# Upgrade Instructions for Read Model for Product Lists from Elasticsearch

There is a new layer in Shopsys Framework, called [read model](/docs/model/introduction-to-read-model.md), separating the templates and application model.

This upgrade requires the [Upgrade Instructions for Read Model for Product Lists](/docs/upgrade/upgrade-instructions-for-read-model-for-product-lists.md) to be applied first.

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
