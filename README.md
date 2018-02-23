# Monorepo

This document provides basic information about development in monorepo to make the work with packages and project-base repository as easy as possible.

If you want to contribute to Shopsys Framework or to any of its packages,
clone this monorepo [shopsys/shopsys](https://github.com/shopsys/shopsys).

If you want to build your project on Shopsys Framework,
clone [shopsys/project-base](https://github.com/shopsys/project-base).

## Problem
Due to the growing number of new repositories, there were many situations when a developer had to reflect the same change
into more than one package. It meant, that the developer had to implement this in the separated repositories of each package.
This approach was inefficient and repeated process always brought increased errors rate.

## Solution
Monorepo approach provides a single development environment for management of all parts of Shopsys Framework.
We use [Monorepo tool](https://github.com/shopsys/monorepo-tools) that splits code in appropriate repositories
after some changes are made in monorepo. This splitting is initiated automatically once a day.

## Repositories maintained by monorepo

* [shopsys/project-base](https://github.com/shopsys/project-base)
* [shopsys/product-feed-zbozi](https://github.com/shopsys/product-feed-zbozi)
* [shopsys/product-feed-google](https://github.com/shopsys/product-feed-google)
* [shopsys/product-feed-heureka](https://github.com/shopsys/product-feed-heureka)
* [shopsys/product-feed-heureka-delivery](https://github.com/shopsys/product-feed-heureka-delivery)
* [shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface)
* [shopsys/plugin-interface](https://github.com/shopsys/plugin-interface)
* [shopsys/coding-standards](https://github.com/shopsys/coding-standards)
* [shopsys/http-smoke-testing](https://github.com/shopsys/http-smoke-testing)
* [shopsys/form-types-bundle](https://github.com/shopsys/form-types-bundle)
* [shopsys/migrations](https://github.com/shopsys/migrations)
* [shopsys/monorepo-tools](https://github.com/shopsys/monorepo-tools)

## Infrastructure
Monorepo can be installed and used as standard application. This requires some additional infrastructure:

* **docker/** - templates for configuration of docker in monorepo.

* **build.xml** - definitions of targets for use in the monorepo, some already defined targets
have modified behaviour in such a way that their actions are launched over all monorepo packages

* **composer.json** - contains the dependencies required by individual packages and by Shopsys Framework.
It is not generated automatically, so each change made in the `composer.json` of the specific package must be reflected
also in `composer.json` in the root of monorepo. In monorepo, Shopsys packages are used directly from the directory
`packages/`, so there are no requirements of those packages in `composer.json`. The exception is the coding-standards
package that continues to be installed in the vendor because the current master version of the package in
`packages/` is not supported by Shopsys Framework.

* **parameters_monorepo.yml** - overriding of global variables of Shopsys Framework, which makes it possible to run 
Shopsys Framework in monorepo

## Development in monorepo
During the development in monorepo, it is necessary to ensure that the changes made in specific package
preserve the functionality of the package even outside the monorepo.
 
Keep in mind that the file structure of Shopsys Framework (standardly located in the root of the project) is in monorepo
located in the directory `project-base/`

Installation of Shopsys Framework is described in [Shopsys Framework installation guide](./project-base/docs/introduction/installation-guide.md)

## Troubleshooting
* Package is functional in monorepo but broken outside of monorepo - ensure that every parameter required by package
is available even outside the monorepo

* Command `cp app/config/domains_urls.yml.dist app/config/domains_urls.yml` results in failure - during the development
in monorepo, Shopsys Framework is placed in the directory `project-base/`. The correct form of this command during the
development in monorepo is `cp project-base/app/config/domains_urls.yml.dist project-base/app/config/domains_urls.yml`
