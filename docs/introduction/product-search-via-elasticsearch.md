# Product Searching via Elasticsearch
To provide the best possible performance, frontend product searching and autocomplete
leverages [Elasticsearch technology](https://www.elastic.co/products/elasticsearch).
Elasticsearch is a super fast no-SQL database where data are stored in JSON format as so-called [documents](https://www.elastic.co/guide/en/elasticsearch/reference/current/_basic_concepts.html#_document) in one or more [indexes](https://www.elastic.co/guide/en/elasticsearch/reference/current/_basic_concepts.html#_index).

## How does it work
All product data are stored in PostgreSQL by default but searching in the relational database might not be fast enough.
Therefore, relevant product attributes are also stored in Elasticsearch index under the same ID.
When the product search action is performed on frontend, the query is send to Elasticsearch.
As a result, found product IDs are returned from Elasticsearch and then the product data are loaded from PostgreSQL database into entities using Doctrine ORM.

### Elasticsearch index setting
Elasticsearch [index](https://www.elastic.co/blog/what-is-an-elasticsearch-index) is a logical namespace, you can imagine single index as a single database in terms of relational databases.

The Elasticsearch indexes are created during application build.
You can also create or delete indexes manually using phing targets `elasticsearch-indexes-create`, and `elasticsearch-indexes-delete` respectively,
or you can use `elasticsearch-indexes-recreate` that encapsulates the previous two.

Unique index is created for each domain as some product attributes can have distinct values for each domain.
To discover the exact mapping setting, you can look at the JSON configuration files
that are located in `src/Resources/elasticsearch/` directory in [`shopsys/framework`](https://github.com/shopsys/framework) package.
The directory is configured using `%shopsys.framework.elasticsearch_sources_dir%` parameter.

### Product data export
No data are automaticly stored in Elasticsearch by "itself". When you store data into a relational database, they are not stored in Elasticsearch.
You have to export data from database into Elasticsearch actively.

Following product attributes are exported into Elasticsearch (i.e. the search is performed on these fields only):
* name
* catnum
* partno
* ean
* description
* short description

Data of all products are exported into Elasticsearch by CRON module (`ElasticsearchExportCronModule`) once an hour.
Alternatively, you can force the export manually using `elasticsearch-products-export` phing target.

### Searching for products

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
* ean - match in first couple of letters
* catnum - match in first couple of letters
* partno - match in first couple of letters
* short description - match anywhere
* description - match anywhere

The searched fields and their priority are defined directly in the `ElasticsearchSearchClient::createQuery()` function.

If you want to improve searching, you can learn more in [Elasticsearch analysis](https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis.html).

## Where does Elasticsearch run?
When using docker installation, Elasticsearch API is available on the address [http://127.0.0.1:9200](http://127.0.0.1:9200).

## How to change the default index, data export setting, and searching behaviour?
If you wish to reconfigure the indexes setting, simply change the `%shopsys.framework.elasticsearch_sources_dir%` parameter
to your custom directory and put your own JSON configurations in it using the same naming pattern (`<domain_id>.json`).

If you need to change the data that are exported into Elasticsearch, overwrite appropriate methods in `ElasticsearchProductRepository` and `ElasticsearchProductDataConverter` classes.

You can also change the searching behavior by overwriting `ElasticsearchSearchClient` class.

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
