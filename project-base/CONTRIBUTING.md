# Contributing

You can take part in making Shopsys Framework better.

* [Create a pull request](https://git.shopsys-framework.com/shopsys/shopsys-framework/merge_requests/new)
* [Report an issue](https://git.shopsys-framework.com/shopsys/shopsys-framework/issues/new)
* [Guidelines for Creating Commits](docs/contributing/guidelines-for-creating-commits.md)
* [Guidelines for Writing Documentation](docs/contributing/guidelines-for-writing-documentation.md)

For your code to be accepted, you should follow our guidelines mentioned above,
and the code must pass [coding standards](docs/contributing/coding-standards.md) checks and tests:
```
php phing standards tests tests-acceptance
```

*Tip: Read more about automatic checks in [Phing Targets](docs/introduction/phing-targets.md) and [Running Acceptance Tests](docs/introduction/running-acceptance-tests.md).*

**Warning: It is necessary to put information about your code being provided under MIT license in the description of your pull request.**
