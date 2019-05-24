# Introduction to Read Model

Read model is an extra layer that separates templates and [the application model](/docs/model/introduction-to-model-architecture.md).
The read model is taken from [CQRS pattern](https://martinfowler.com/bliki/CQRS.html) where the main idea is usage of different objects for reading and writing data.

![model architecture schema](./img/read-model-architecture.png 'Read model in Shopsys Framework architecture')

The read model stands next to the standard domain model, and it is completely independent of it. When using [entities](./entities.md) from the standard domain model, you might get a lot of data that you do not need for your particular use case.
This is not effective and often has a negative impact on the application performance.
Also, entities have a lot of responsibilities that are useless (or even harmful) during reading scenarios.

The main goal of the read model in Shopsys Framework is a clear separation of the model and view layer, and performance gain for the end user.
This is achieved by avoiding the usage of Doctrine entities (and hence calls to SQL Database) in particular frontend templates.

Each object in the read model has its specific purpose (e.g. there is [`ListedProductView`](/packages/read-model/src/Product/Listed/ListedProductView.php) object that is used on product lists only).
Unlike the entities, objects in the read model contain solely the information that are necessary for a particular use case
and their data can be gathered from various sources (eg. Elasticsearch storage, and session).
Read model is a view on the model from a specific perspective - from the reading view. You are using the read model for reading use-cases only and therefore the read model can be simple and very optimized.

The objects in read model are immutable, read-only by definition, and do not have any behavior.

The read model is implemented in a separate [`shopsys/read-model`](https://github.com/shopsys/read-model) package.