# Contributing

You can take part in making Shopsys Framework better.

* [Create a pull request](https://github.com/shopsys/shopsys/compare)
* [Report an issue](https://github.com/shopsys/shopsys/issues/new)
* [Backward Compatibility Promise](/docs/contributing/backward-compatibility-promise.md)
* [Guidelines for Working with Monorepo](./docs/introduction/monorepo.md)
* [Guidelines for Creating Commits](./docs/contributing/guidelines-for-creating-commits.md)
* [Guidelines for Writing Documentation](./docs/contributing/guidelines-for-writing-documentation.md)
* [Guidelines for Pull Request](./docs/contributing/guidelines-for-pull-request.md)
* [Guidelines for Dependencies](./docs/contributing/guidelines-for-dependencies.md)
* [Guidelines for writing UPGRADE.md](./docs/contributing/guidelines-for-writing-upgrade.md)
* [Merging to Master on Github](./docs/contributing/merging-to-master-on-github.md)
* [Releasing a new version of Shopsys Framework monorepo](docs/contributing/releasing-a-new-version-of-shopsys-framework.md)

For your code to be accepted, you should follow our guidelines mentioned above,
and the code must pass [coding standards](./docs/contributing/coding-standards.md) checks and tests:
```
php phing standards tests tests-acceptance
```

Your code may not infringe the copyrights of any third party.
If you are changing a composer's dependency in composer.json or you are changing the npm dependencies in package.json, you need to reflect this change into a list of [Open Source License Acknowledgements and Third Party Copyrights](./open-source-license-acknowledgements-and-third-party-copyrights.md).
Apply the same procedure if you make the changes in Dockerfile or docker-compose.yml files.

These rules ensure that the code will remain consistent and the project is maintainable in the future.

*Tip: Read more about automatic checks in [Console Commands for Application Management (Phing Targets)](./docs/introduction/console-commands-for-application-management-phing-targets.md) and [Running Acceptance Tests](./docs/introduction/running-acceptance-tests.md).*
