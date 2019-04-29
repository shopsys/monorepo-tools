# UPGRADING
The releases of Shopsys Framework adhere to the [Backward Compatibility Promise](/docs/contributing/backward-compatibility-promise.md) to make upgrades to new versions easier and help long-term maintainability.

## Recommended way of upgrading
Since these are 3 possible scenarios how you can use shopsys, instructions are divided by these scenarios.

### You use our packages only
Follow instructions in relevant sections, eg. `shopsys/coding-standards`, `shopsys/framework`.

### You are using monorepo
Follow instructions in a [monorepo upgrade guide](docs/contributing/upgrading-monorepo.md)

### You are developing a project based on project-base
* upgrade only your composer dependencies and follow instructions
* check all instructions in all sections, any of them could be relevant for you
* if you want update your project with the changes from [shopsys/project-base],
    you should cherry-pick from the repository whatever is relevant for you but we do not recommend rebasing or merging everything because the changes might not be compatible with your project as it probably evolves in time
* upgrade locally first. After you fix all issues caused by the upgrade, commit your changes and then continue with upgrading application on a server
* upgrade one version at a time:
    * Start with a working application
    * Upgrade to the next version
    * Fix all issues
    * Repeat
* typical upgrade sequence should be:
    * run `docker-compose down` to turn off your containers
    * *(MacOS, Windows only)* run `docker-sync stop`
    * *(MacOS, Windows only)* run `docker-sync clean` so your volumes will be removed
    * follow upgrade notes in a *Infrastructure* section (related with `docker-compose.yml`, `Dockerfile`, docker containers, `nginx.conf`, `php.ini`, etc.)
    * *(MacOS, Windows only)* run `docker-sync start` to create volumes  
    * run `docker-compose up -d --build --force-recreate --remove-orphans` to start application again
    * update shopsys framework dependencies in `composer.json` to version you are upgrading to
        eg. `"shopsys/framework": "v7.0.0"`
    * `composer update`
    * follow all upgrade notes you have not done yet
    * `php phing clean`
    * run `php phing db-migrations` to run database migrations
    * commit your changes
* if any of the database migrations does not suit you, there is an option to skip it, see [our Database Migrations docs](https://github.com/shopsys/shopsys/blob/master/docs/introduction/database-migrations.md#reordering-and-skipping-migrations)
* even we care a lot about these instructions, it is possible we miss something. In case something doesn't work after the upgrade, you'll find more information in the [CHANGELOG](CHANGELOG.md)

## Upgrade
* ### [From v7.1.0 to Unreleased](./docs/upgrade/UPGRADE-unreleased.md)
* ### [From v7.0.0 to v7.1.0](./docs/upgrade/UPGRADE-v7.1.0.md)
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

[shopsys/project-base]: https://github.com/shopsys/project-base
