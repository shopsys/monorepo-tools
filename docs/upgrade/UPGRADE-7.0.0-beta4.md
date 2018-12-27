## [From v7.0.0-beta3 to v7.0.0-beta4](https://github.com/shopsys/shopsys/compare/v7.0.0-beta3...v7.0.0-beta4)
### [shopsys/project-base]
- [#616 - services.yml: automatic registration of classes with suffix "Repository" in namespace ShopBundle\Model\ ](https://github.com/shopsys/shopsys/pull/616)
    - modify your `src/Shopsys/ShopBundle/Resources/config/services.yml`, change the resource for automatic registration of Model services from `resource: '../../Model/**/*{Facade,Factory}.php'` to `resource: '../../Model/**/*{Facade,Factory,Repository}.php'`
- set `ENV COMPOSER_MEMORY_LIMIT=-1` in base stage in your `docker/php-fpm/Dockerfile` as composer consumes huge amount of memory during dependencies installation
    - see [#635 - allow composer unlimited memory](https://github.com/shopsys/shopsys/pull/635/files)

[shopsys/shopsys]: https://github.com/shopsys/shopsys
[shopsys/project-base]: https://github.com/shopsys/project-base
[shopsys/framework]: https://github.com/shopsys/framework
[shopsys/product-feed-zbozi]: https://github.com/shopsys/product-feed-zbozi
[shopsys/product-feed-google]: https://github.com/shopsys/product-feed-google
[shopsys/product-feed-heureka]: https://github.com/shopsys/product-feed-heureka
[shopsys/product-feed-heureka-delivery]: https://github.com/shopsys/product-feed-heureka-delivery
[shopsys/product-feed-interface]: https://github.com/shopsys/product-feed-interface
[shopsys/plugin-interface]: https://github.com/shopsys/plugin-interface
[shopsys/coding-standards]: https://github.com/shopsys/coding-standards
[shopsys/http-smoke-testing]: https://github.com/shopsys/http-smoke-testing
[shopsys/form-types-bundle]: https://github.com/shopsys/form-types-bundle
[shopsys/migrations]: https://github.com/shopsys/migrations
[shopsys/monorepo-tools]: https://github.com/shopsys/monorepo-tools
[shopsys/microservice-product-search]: https://github.com/shopsys/microservice-product-search
[shopsys/microservice-product-search-export]: https://github.com/shopsys/microservice-product-search-export
