# Elasticsearch
To provide the best possible performance, frontend product searching, filtering and autocomplete
leverages [Elasticsearch technology](https://www.elastic.co/products/elasticsearch).
Elasticsearch is a super fast no-SQL database where data are stored in JSON format as so-called [documents](https://www.elastic.co/guide/en/elasticsearch/reference/current/_basic_concepts.html#_document) in one or more [indexes](https://www.elastic.co/guide/en/elasticsearch/reference/current/_basic_concepts.html#_index).

## How does it work
All product data are stored in PostgreSQL by default but querying relational database might not be fast enough.
Therefore, relevant product attributes are also stored in Elasticsearch index under the same ID.
When products need to be searched or filtered on the frontend, the query is sent to Elasticsearch.
As a result, found product IDs are returned from Elasticsearch and then the product data are loaded from PostgreSQL database into entities using Doctrine ORM.

### Elasticsearch index setting
Elasticsearch [index](https://www.elastic.co/blog/what-is-an-elasticsearch-index) is a logical namespace, you can imagine single index as a single database in terms of relational databases.

The Elasticsearch indexes are created during application build.
You can also create or delete indexes manually using phing targets `product-search-create-structure`, and `product-search-delete-structure` respectively, or you can use `product-search-recreate-structure` that encapsulates the previous two.

*Note: More information about what Phing targets are and how they work can be found in [Console Commands for Application Management (Phing Targets)](/docs/introduction/console-commands-for-application-management-phing-targets.md)*

Unique index is created for each domain as some product attributes can have distinct values for each domain.
To discover the exact mapping setting, you can look at the JSON configuration files
that are located in `src/Shopsys/ShopBundle/Resources/definition/` directory in [`shopsys/project-base`](https://github.com/shopsys/project-base).
The directory is configured using `%shopsys.elasticsearch.structure_dir%` parameter.

### Product data export
No data are automatically stored in Elasticsearch by "itself".
When you store data into a relational database, they are not stored in Elasticsearch.
You have to export data from the database into Elasticsearch actively.

Following product attributes are exported into Elasticsearch (i.e. the search or filtering can be performed on these fields only):
* name
* catnum
* partno
* ean
* description
* short description
* flags (IDs of assigned flags)
* brand (ID of assigned brand)
* categories (IDs of assigned categories)
* prices (all the prices for all pricing groups)
* in_stock (true/false value whether the product is in stock)
* parameters (pairs of parameter IDs and parameter value IDs)
* ordering_priority (priority number)
* calculated_selling_denied (true/false value whether the product is already sold out)

Data of all products are exported into Elasticsearch by CRON module (`ProductSearchExportCronModule.php`) once an hour.
Alternatively, you can force the export manually using `product-search-export-products` Phing target.

## Use of Elasticsearch
Elasticsearch is used to search, filter and sort products on the frontend.
You can learn more about [Product searching](/docs/model/front-end-product-searching.md) and [Product filtering](/docs/model/front-end-product-filtering.md) in particular articles.
[Sorting](/docs/introduction/how-to-set-up-domains-and-locales.md#37-sorting-in-different-locales) is done with the help of [ICU analysis plugin](https://www.elastic.co/guide/en/elasticsearch/plugins/current/analysis-icu.html)
which ensures that alphabetical sorting is correct for every language and its set of rules.

## Where does Elasticsearch run?
When using docker installation, Elasticsearch API is available on the address [http://127.0.0.1:9200](http://127.0.0.1:9200).

## How to change the default index and data export setting?
If you wish to reconfigure the indexes setting, simply change the JSON configurations in `src/Shopsys/ShopBundle/Resources/Resources/definition/`.
Configurations use the `<index>/<domain_id>.json` naming pattern.

If you need to change the data that are exported into Elasticsearch, overwrite appropriate methods in `ProductSearchExportWithFilterRepository` and `ProductElasticsearchConverter` classes.

## Known issues
* When you need to add a new domain, you have to do following steps
  * create elasticsearch definition for the domain
  * delete indexes
  * create indexes
  * export products

## Troubleshooting
* You can easily check if there is a product exported in the Elasticsearch by putting following url address into your browser
  `http://127.0.0.1:9200/{domain ID}/_doc/{product ID}?pretty`
  eg. `http://127.0.0.1:9200/1/_doc/52?pretty`

* If the export fails with a following error (or similar)
  `
  [Elasticsearch\Common\Exceptions\Forbidden403Exception (403)]
  ...{"type": "cluster_block_exception", "reason": "blocked by: [FORBIDDEN/12/index read-only / allow delete (api)];"}...
  `
  It means the Elasticsearch switched into a read-only mode. Possible reason is that you have almost full disk, default value when Elasticsearch switch into read-only mode is `95%`.

  Solution is to make more space on your hard drive, and then manually release the read-only mode by running followin console command:
  `curl -XPUT -H "Content-Type: application/json" http://localhost:9200/_all/_settings -d '{"index.blocks.read_only_allow_delete": null}'`

  You can find more information in [https://www.elastic.co/guide/en/elasticsearch/reference/6.2/disk-allocator.html](https://www.elastic.co/guide/en/elasticsearch/reference/6.2/disk-allocator.html).
