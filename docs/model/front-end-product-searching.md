# Front-end Product Searching
We use the same method for searching and for autocomplete, so results are always the same.

Understanding Elasticsearch searching is difficult.
But if we simplify, we can say that the search term is searched in attributes and is prioritized in following order:
* ean - exact match
* name - match any of words
* name - match any of words ignoring diacritics
* catnum - exact match
* name - match any of words in root form
* partno - exact match
* name - match in first couple of letters of any word
* name - match in first couple of letters of any word ignoring diacritics
* ean - exact match in first characters
* catnum - exact match in first characters
* partno - exact match in first characters
* short description - match anywhere
* description - match anywhere

If you want to improve searching, you can learn more in [Elasticsearch analysis](https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis.html).

## Change searching behavior
Searching is performed with `ProductElasticsearchRepository` class, more specifically its method `getProductIdsBySearchText()`.
This method gets product IDs from Elasticsearch with a query that is represented by `Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery` object.

The searched fields and their priority are defined directly in the `FilterQuery::search()` method,
so to change the search behavior is enough to extend the `FilterQuery` class and use your implementation in `services.yml` file
```yaml
Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery:
    alias: Shopsys\ShopBundle\Model\Product\Search\FilterQuery
```
