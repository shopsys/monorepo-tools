# UPGRADING
The releases of Shopsys Framework adhere to the [Backward Compatibility Promise](/docs/contributing/backward-compatibility-promise.md) to make the upgrades to new versions easier and help long-term maintainability.

## Recommended way of upgrading
Since there are 3 possible scenarios how you can use the Shopsys Framework, instructions are divided into these scenarios.

### You use our packages only
Follow the instructions in relevant sections, eg. `shopsys/coding-standards` or `shopsys/http-smoke-testing`.

### You are using the monorepo
Follow the instructions in the [monorepo upgrade guide](docs/contributing/upgrading-monorepo.md).

### You are developing a project based on the project-base repository
* upgrade only your composer dependencies and follow the instructions in a guide below
* upgrade locally first - after you fix all issues caused by the upgrade, commit your changes, test your application and then continue with a deployment onto your server
* upgrade one version at a time:
    * start with a working application
    * upgrade to the next version
    * fix all the issues you encounter
    * repeat
* check the instructions in all sections, any of them could be relevant for you
* typical upgrade sequence should be:
    * run `docker-compose down` to turn off your containers
    * *(MacOS, Windows only)* run `docker-sync clean` so your volumes will be stopped and removed
    * follow upgrade notes in the *Infrastructure* section (related with `docker-compose.yml`, `Dockerfile`, docker containers, `nginx.conf`, `php.ini`, etc.)
    * *(MacOS, Windows only)* run `docker-sync start` to create volumes  
    * run `docker-compose build --no-cache --pull` to build your images without cache and with latest version
    * run `docker-compose up -d --force-recreate --remove-orphans` to start the application again
    * update the `shopsys/*` dependencies in `composer.json` to version you are upgrading to
        eg. `"shopsys/framework": "v7.0.0"`
    * run `composer update`
    * follow all upgrade notes you have not done yet
    * run `php phing clean`
    * run `php phing db-migrations` to run the database migrations
    * test your app locally
    * commit your changes
* if any of the database migrations does not suit you, there is an option to skip it, see [our Database Migrations docs](https://github.com/shopsys/shopsys/blob/master/docs/introduction/database-migrations.md#reordering-and-skipping-migrations)
* even we care a lot about these instructions, it is possible we miss something. In case something doesn't work after the upgrade, you'll find more information in the [CHANGELOG](CHANGELOG.md)

## Upgrade guides
* ### [From v8.0.0 to Unreleased](./docs/upgrade/UPGRADE-unreleased.md)
* ### [From v7.3.1 to v8.0.0](docs/upgrade/UPGRADE-v8.0.0.md)
* ### [From v7.3.0 to v7.3.1](./docs/upgrade/UPGRADE-v7.3.1.md)
* ### [From v7.2.2 to v7.3.0](./docs/upgrade/UPGRADE-v7.3.0.md)
* ### [From v7.2.1 to v7.2.2](./docs/upgrade/UPGRADE-v7.2.2.md)
* ### [From v7.2.0 to v7.2.1](./docs/upgrade/UPGRADE-v7.2.1.md)
* ### [From v7.1.0 to v7.2.0](./docs/upgrade/UPGRADE-v7.2.0.md)
* ### [From v7.1.0 to v7.1.1](./docs/upgrade/UPGRADE-v7.1.1.md)
* ### [From v7.0.0 to v7.1.0](./docs/upgrade/UPGRADE-v7.1.0.md)
* ### [From v7.0.0 to v7.0.1](./docs/upgrade/UPGRADE-v7.0.1.md)
* ### [From v7.0.0-beta6 to v7.0.0](./docs/upgrade/UPGRADE-v7.0.0.md)
* ### [From v7.0.0-beta5 to v7.0.0-beta6](./docs/upgrade/UPGRADE-v7.0.0-beta6.md)
* ### [From v7.0.0-beta4 to v7.0.0-beta5](./docs/upgrade/UPGRADE-v7.0.0-beta5.md)
* ### [From v7.0.0-beta3 to v7.0.0-beta4](docs/upgrade/UPGRADE-v7.0.0-beta4.md)
* ### [From v7.0.0-beta2 to v7.0.0-beta3](docs/upgrade/UPGRADE-v7.0.0-beta3.md)
* ### [From v7.0.0-beta1 to v7.0.0-beta2](docs/upgrade/UPGRADE-v7.0.0-beta2.md)
* ### [From v7.0.0-alpha6 to v7.0.0-beta1](docs/upgrade/UPGRADE-v7.0.0-beta1.md)
* ### [From v7.0.0-alpha5 to v7.0.0-alpha6](docs/upgrade/UPGRADE-v7.0.0-alpha6.md)
* ### [From v7.0.0-alpha4 to v7.0.0-alpha5](docs/upgrade/UPGRADE-v7.0.0-alpha5.md)
* ### [From v7.0.0-alpha3 to v7.0.0-alpha4](docs/upgrade/UPGRADE-v7.0.0-alpha4.md)
* ### [From v7.0.0-alpha2 to v7.0.0-alpha3](docs/upgrade/UPGRADE-v7.0.0-alpha3.md)
* ### [From v7.0.0-alpha1 to v7.0.0-alpha2](docs/upgrade/UPGRADE-v7.0.0-alpha2.md)
* ### [Before monorepo](docs/upgrade/before-monorepo.md)
