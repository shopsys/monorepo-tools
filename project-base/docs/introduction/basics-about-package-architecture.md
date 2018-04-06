# Basics About Package Architecture

## Introduction
This article describes the architecture of Shopsys Framework from the viewpoint of packages
(in a contrast to application layers, about which you can read in the [separate article](basics-about-model-architecture.md)).
After you finish reading the article, you will know 
how to start an implementation, how to perform upgrades of your own project based on Shopsys Framework
and you will understand what modules are.

## Basic terms
In order to make the architecture description understandable, it is necessary to define a few basic terms first.

### Open-box extensibility
The [open-box concept](https://en.wikipedia.org/wiki/Extensibility#Open-Box) enables you to directly change the source code and as a result, permits unrestricted customizability.
We believe that lean codebase that is easily modifiable is a better place to start building your project than a feature-rich platform
with a lot of configurable options.

The [`shopsys/project-base`](https://github.com/shopsys/project-base)
only contains frontend part of the framework, ie. controllers and views, excluding web admin.
The package is open-box so you will create your own copy of the package and then you will directly modify source codes for your concrete project.

### Glass-box extensibility
[Glass-box extensibility](https://en.wikipedia.org/wiki/Extensibility#Glass-Box) does not allow any modifications to the original package code and therefore creates
a clear separation between your code and the code maintained by somebody else (Composer places this code in the vendor directory).

The [`shopsys/framework`](https://github.com/shopsys/framework) 
contains business logic of a basic online store, including web admin, and is designed as a glass-box.
Shopsys Framework modules are glass-box too and are described below. 

### Modules
In every project, there is a lot of code dedicated to features which are not related to the core of your business,
but you still need it there. And although these features are necessary, it can be a long wearisome job building them from the ground up.
That is why Shopsys Framework provides a module system that satisfies the need for an installable functionality.
Modules are developed in separate packages with [semantic versioning](http://semver.org/).
You can install a module just by requiring its package via Composer and registering it in your application.

So far, we created packages for [HTTP Smoke testing](https://github.com/shopsys/http-smoke-testing) or [database migrations](https://github.com/shopsys/migrations),
and extracted product XML feeds (eg. [Google Shopping product feed](https://github.com/shopsys/product-feed-google)).
Other candidates for extraction into modules are for example payment methods gateways, package shipping integrations or analytic service integrations.

![Shopsys Framework package architecture schema](img/package-architecture.png)

*Note: The specific modules in this diagram are just examples.*

## How to develop your project on Shopsys Framework
### Create new project from Shopsys Framework sources
Install [`shopsys/project-base`](https://github.com/shopsys/project-base) using composer to get your own private copy.
```
composer create-project shopsys/project-base --stability=alpha --no-install
```
For more detailed instructions, follow [the installation guide](../docker/installation/installation-using-docker.md).
#### Why not clone or fork?
`composer create-project` ensures that new project will be created from the latest release of `shopsys/project-base`.
`git clone` creates a new project from current repository master branch.
We do not recommend forking for the same reason.
Forking also copies the `shopsys/project-base` under your Gihub account and the copy is public by default, and you probably do not need that.

### Upgrading
We know that upgrading should be as easy as possible, ideally without requiring any modification of your code.
This goal is very hard to achieve while both providing unlimited customizability and innovating the framework itself,
as the project’s every customization has to work well with every new release.

#### Framework and modules
The framework and modules provide glass-box extensibility,
ie. you can upgrade them independently via `composer update`.

#### Project base
There is no automated way of upgrading the project base.
If you wanted to upgrade your project base, you would need to [cherry-pick](https://git-scm.com/docs/git-cherry-pick) or [merge](https://git-scm.com/docs/git-merge) modifications from the original repository manually.
We try to ease you the process as much as possible by maintaining clear GIT history,
keeping up-to-date [changelog](../../CHANGELOG.md) and [upgrading instructions](../../UPGRADE.md),
and writing [understandable commit messages](../../docs/contributing/guidelines-for-creating-commits.md).

## Conclusion
* You learned it is necessary to use composer to initialize development of your own project based on the framework.
* You understand the open-box concept - you can modify the project base as you wish without any restrictions.
* You know that the business logic is placed in the [`shopsys/framework`](https://github.com/shopsys/framework).
* You are familiar with modules that can ease you a development of your project.
* You know you are able to upgrade the framework and modules using composer thanks to their glass-box design.
