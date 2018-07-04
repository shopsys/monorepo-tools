# Basic and Demo Data During Application Installation

All basic data that are vital for Shopsys Framework (e.g. administrator, vat, database functions and triggers, etc.) are created in [database migrations](./database-migrations.md).

As the migrations create data for the first domain only, 
after all migrations are executed, necessary data must be created for all the other domains
(e.g. multidomain settings like free transport limit, database indexes for new locale etc.).
This is the responsibility of `phing` task `create-domains-data` that executes [`CreateDomainsDataCommand`](./../../packages/framework/src/Command/CreateDomainsDataCommand.php).

All the other data that are not vital (products, customers, etc.) are created afterwards as data fixtures (i.e. demo data)
using `phing` targets `db-fixtures-demo-singledomain` and (if there is more than one domain) `db-fixtures-demo-multidomain`.  