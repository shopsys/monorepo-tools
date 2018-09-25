# Microservice Product Search Export

[![Build Status](https://travis-ci.org/shopsys/microservice-product-search-export.svg?branch=master)](https://travis-ci.org/shopsys/microservice-product-search-export)

Microservice responsible for product export into Elasticsearch on [Shopsys Framework](https://www.shopsys-framework.com).

This repository is maintained by [shopsys/shopsys](https://github.com/shopsys/shopsys) monorepo, information about the changes are in [monorepo CHANGELOG.md](https://github.com/shopsys/shopsys/blob/master/CHANGELOG.md).

## How it works
Microservice acts as a fully independent unit.
It uses its own separate server, separate logic and it uses own vendor directory for Composer dependencies.
All of these parts are located in a separate Docker container.
This container is configured in [docker-compose.yml](https://github.com/shopsys/shopsys/tree/master/docker/conf) under the name `microservice-product-search-export`

As the base of the microservice uses micro-kernel which is made up of [Symfony 4](https://symfony.com/4).
This leanest Symfony version is highly optimized and it is suitable for this type of project.

The microservice is responsible for feeding the Elasticsearch by product data so [product search microservice](https://github.com/shopsys/microservice-product-search) can work properly.

## Installation
Microservice is installed by running container from image, during build of image all dependencies gets installed and nginx with php-fpm si configured to allow traffic into microservice.

If you don't want to build the Docker image yourself, you can use [`shopsys/microservice-product-search-export:latest`](https://hub.docker.com/r/shopsys/microservice-product-search-export/).

If you don't use Docker, please check [Shopsys Framework Native Installation Guide](https://github.com/shopsys/shopsys/blob/master/docs/installation/native-installation.md) to see how to install and run it natively.

## How to use

The microservice is available by HTTP protocol and is called from Shopsys Framework by standard HTTP requests.

### Check availability

Checks that microservice is running. You can only get `200 OK` response or message that the endpoint was not found.

#### Request
```http
GET / HTTP/1.1
Host: microservice-product-search-export:8000
Content-Type: application/json
```

#### Response
```http
HTTP/1.1 200 OK
Content-Type: application/json

{
    "info": "running"
}
```

### Create structure

Creates Elasticsearch index structure for a domain. This operation must be performed before updating products.

#### Parameters
* `{domainId} - int, ID of domain`

#### Request
```http
POST /{domainId} HTTP/1.1
Host: microservice-product-search-export:8000
Content-Type: application/json
```

#### Response - structure successfully created
```http
HTTP/1.1 200 OK
Content-Type: application/json

{}
```

#### Response - structure exists, so it was not created
```http
HTTP/1.1 500 Internal Server Error
Content-Type: application/json

{
    "message": "Index 1 already exists"
}
```

### Delete structure

Deletes Elasticsearch index structure for a domain.
Deleting not existing index doesn't produce an error.

#### Parameters
* `{domainId} - int, ID of domain`

#### Request
```http
DELETE /{domainId} HTTP/1.1
Host: microservice-product-search-export:8000
Content-Type: application/json
```

#### Response
```http
HTTP/1.1 200 OK
Content-Type: application/json

{}
```

### Update products

Updates existing products and also creates new ones if they do not exists. This operation doesn't delete products.

#### Parameters
* `{domainId} - int, ID of domain`

#### Body
```
{
    {productId}: {
        {attributeKey}: {attributeValue},
        ...
    },
    ...
}
```

#### Request
```http
PATCH /{domainId}/products HTTP/1.1
Host: microservice-product-search-export:8000
Content-Type: application/json

{
    "1": {
        "catnum": "9177759",
        "partno": "SLE 22F46DM4",
        "ean": "8845781245930",
        "name": "22\" Sencor SLE 22F46DM4 HELLO KITTY",
        "description": "...",
        "shortDescription": "Sencor..."
    },
    "58": {
        "catnum": "789",
        "partno": "SLX 45945D87",
        "ean": "5875781245548",
        "name": "Genius repro SP-M120 black",
        "description": "...",
        "shortDescription": "Genius ..."
    }
}
```

#### Response - successfully updated
```http
HTTP/1.1 200 OK
Content-Type: application/json

{}
```

#### Response - errors
```http
HTTP/1.1 500 Internal Server Error
Content-Type: application/json

{
    "message": "reason ..."
}
```

### Delete products

We define what products we want to keep during deleting, everything else is deleted.
We do this because from the Shopsys Framework we don't know what is and what isn't in Elasticsearch.

#### Parameters
* `{domainId} - int, ID of domain`

#### Body
```
{
    "keep": [{productId1}, {productId2}, ...]
}
```

#### Request
```http
DELETE /{domainId}/products HTTP/1.1
Host: microservice-product-search-export:8000
Content-Type: application/json

{
    "keep": [1, 2, 57, 58]
}
```

#### Response
```http
HTTP/1.1 200 OK
Content-Type: application/json

{}
```

## Logging
Microservice Product Search is a Symfony based application that uses [Monolog](https://github.com/Seldaek/monolog) with [symfony/monolog-bundle](https://github.com/symfony/monolog-bundle) as a logging tool.
By default, the logs are routed into a named pipe `/tmp/log-pipe` (the same way as [Logging in the main application](https://github.com/shopsys/shopsys/blob/master/docs/introduction/logging.md) works).

To see the logs simply run:
```
docker-compose logs microservice-product-search-export
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
