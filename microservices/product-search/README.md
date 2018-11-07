# Microservice Product Search

Microservice responsible for product search via Elasticsearch on [Shopsys Framework](https://www.shopsys-framework.com).

This repository is maintained by [shopsys/shopsys](https://github.com/shopsys/shopsys) monorepo, information about the changes are in [monorepo CHANGELOG.md](https://github.com/shopsys/shopsys/blob/master/CHANGELOG.md).

## How it works
Microservice acts as a fully independent unit.
It uses its own separate server, separate logic and it uses own vendor directory for Composer dependencies.
All of these parts are located in a separate Docker container.
This container is configured in [docker-compose.yml](https://github.com/shopsys/shopsys/tree/master/docker/conf) under the name `microservice-product-search`

As the base of the microservice, we used micro-kernel which is made up of [Symfony 4](https://symfony.com/4).
This leanest Symfony version is highly optimized and it is suitable for this type of project.

For the search process is used a super fast no-SQL database Elasticsearch. For more information about the Elasticsearch on Shopsys Framework, see [Product search via Elasticsearch]((https://github.com/shopsys/shopsys/blob/master/docs/introduction/product-search-via-elasticsearch.md).)

## Installation
Microservice is installed by running container from image, during build of image all dependencies gets installed and nginx with php-fpm si configured to allow traffic into microservice.

If you don't want to build the Docker image yourself, you can use [`shopsys/microservice-product-search:latest`](https://hub.docker.com/r/shopsys/microservice-product-search/).

If you don't use Docker, please check [Shopsys Framework Native Installation Guide](https://github.com/shopsys/shopsys/blob/master/docs/installation/native-installation.md) to see how to install and run it natively.

## How to use

### MicroserviceClient component
To call the microservice from the Shopsys Framework based application, there is an already prepared component [MicroserviceClient](/packages/framework/src/Component/Microservice/MicroserviceClient.php).
The instance of this component is parametrized with the URL on which the microservice is available.

Instance of `MicroserviceClient` for the Microservice Product Search:
```yaml
# services.yml:

shopsys.microservice_client.product_search:
    factory: 'Shopsys\FrameworkBundle\Component\Microservice\MicroserviceClientFactory:create'
    arguments:
      $microserviceUrl: '%microservice_product_search_url%'
```

### Request
Search with the usage of Microservice Product Search is done by calling the method `get` of `MicroserviceClient`:
```php
public function get(string $resource, array $parameters = [])
{
    // ...  
}
```
A resource is represented with the specific uri address.

They are two required parameters to call the Microservice Product Search:
* **searchText** - the search string
* **domainId** - the e-shop domain to search for.
Some attributes of a product can be different across the domains.

Example:
```php
$this->microserviceProductSearchClient->get('search-product-ids', [
    'searchText' => 'Graphic card',
    'domainId' => 1,
]);
```

### Response
The microservice returns an array of ids of products that match the search string.
Ids of products are ordered by relevance.
The response is represented as the JSON.
```json
{"productIds":[2,1,3]}
```

## Logging
Microservice Product Search is a Symfony based application that uses [Monolog](https://github.com/Seldaek/monolog) with [symfony/monolog-bundle](https://github.com/symfony/monolog-bundle) as a logging tool.
By default, the logs are routed into a named pipe `/tmp/log-pipe` (the same way as [Logging in the main application](https://github.com/shopsys/shopsys/blob/master/docs/introduction/logging.md) works).

To see the logs simply run:
```
docker-compose logs | grep 'microservice'
```

## How to modify the behavior of microservice in a custom project
One of the main ideas of implementing the microservice is to keep its scope as small as possible.
This makes it possible to completely change the behaviour of the microservice by overwriting its code without having to deal with the complex dependencies.
The only thing that must remain preserved is the original minimalist interface.

## Contributing
Thank you for your contributions to Microservice Product Search.
Together we are making Shopsys Framework better.

This repository is READ-ONLY.
If you want to [report issues](https://github.com/shopsys/shopsys/issues/new) and/or send [pull requests](https://github.com/shopsys/shopsys/compare),
please use the main [Shopsys repository](https://github.com/shopsys/shopsys).

Please, check our [Contribution Guide](https://github.com/shopsys/shopsys/blob/master/CONTRIBUTING.md) before contributing.

## Support
What to do when you are in troubles or need some help? The best way is to contact us on our Slack [http://slack.shopsys-framework.com/](http://slack.shopsys-framework.com/)

If you want to [report issues](https://github.com/shopsys/shopsys/issues/new), please use the main [Shopsys repository](https://github.com/shopsys/shopsys).
