# Contributing

You can take part in making Shopsys Framework better.

* [Create a pull request](https://github.com/shopsys/shopsys/compare)
* [Report an issue](https://github.com/shopsys/shopsys/issues/new)
* [Guidelines for Creating Commits](./docs/contributing/guidelines-for-creating-commits.md)
* [Guidelines for Writing Documentation](./docs/contributing/guidelines-for-writing-documentation.md)
* [Guidelines for Pull Request](./docs/contributing/guidelines-for-pull-request.md)
* [Guidelines for Dependencies](./docs/contributing/guidelines-for-dependencies.md)
* [Merging to Master on Github](./docs/contributing/merging-to-master-on-github.md)

For your code to be accepted, you should follow our guidelines mentioned above,
and the code must pass [coding standards](./docs/contributing/coding-standards.md) checks and tests:
```
php phing standards tests tests-acceptance
```

These rules ensure that the code will remain consistent and the project is maintainable in the future.

*Tip: Read more about automatic checks in [Phing Targets](./docs/introduction/phing-targets.md) and [Running Acceptance Tests](./docs/introduction/running-acceptance-tests.md).*
