# Guidelines for writing UPGRADE.md

Keep in mind that upgrade instructions are written for users that do not understand our system well, so more clear they are, more useful they are.

## Introduction

* Our users work in a clone of project-base and even when they do the upgrade, their project-base is not upgraded.
  Every time you change/add anything in project-base, write upgrade instruction how to repeat this work
    * for anything with docker, phing, frontend, config, ...
* Make instructions as easy to follow as possible
    * Good example: [postgres upgrade](https://github.com/shopsys/shopsys/blob/master/UPGRADE.md#postgresql-upgrade)
    * Copyable commands are great
    * Bad example: *"Apply changes done in PR..."*, however link to particular diff is all right
* If you mention a file, make a link for it
    * This is especially important for files in project-base, as users don't have new changes in their project-base
* Link files in an accurate version, because the project evolves in time
    * Good example: [installation using docker - version alpha5](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/docs/installation/installation-using-docker-application-setup.md)
    * Bad example: [installation using docker - master](https://github.com/shopsys/shopsys/blob/master/docs/installation/installation-using-docker-application-setup.md)
* Write instructions
    * Good example: *"Do this, then that"*
    * Bad example: *"This was done, this was changed"*
* If there are any upgrading steps that are not vital for project developers but could be helpful for them,
    they should be mentioned in the upgrading notes as well and marked as (low priority).
    The general UPGRADE.md then mentions that the low priority steps are not vital,
    however we recommend to perform them as well during upgrading as it might ease their work in the future.

## Files related to upgrade

The main file where a project developer should start looking for instructions is [`UPGRADE.md`](../../UPGRADE.md) file in the monorepo root.

This file contains information for the contributors in the form of the link to [`docs/contributing/upgrading-monorepo.md`](upgrading-monorepo.md) file.

Instructions for developers building a project based on project-base should follow.
First, there must be general information about upgrading with recommended steps and a typical upgrade sequence,
following with a list of links to upgrade guides for each version.
These versions should be placed in a [`docs/upgrade/`](../../docs/upgrade) folder.

## Structure of upgrade files

Each upgrade file must have link to main UPGRADE.md file with general information about upgrade and may contain one or more of following main sections:

* shopsys/framework
* shopsys/coding-standards
* shopsys/form-types-bundle
* shopsys/http-smoke-testing
* shopsys/migrations
* shopsys/monorepo-tools
* shopsys/plugin-interface
* shopsys/product-feed-google
* shopsys/product-feed-heureka
* shopsys/product-feed-heureka-delivery
* shopsys/product-feed-zbozi

Each section must contain instructions relevant only to the package they cover and the sections have to be ordered as they are in a list above.

Each step should have a link to the related pull request and may contain an additional link or links to make instruction more clear.

### Section shopsys/framework

Because this section is expected to be the longest, it should contain a finer division into one or more of the following sub-sections:

* Infrastructure
    * related with Docker, Kubernetes, Environment settings, ...
    * instruction to rebuild images must occur only once
* Configuration
    * related with parameters, YML configuration files, ...
* Tools
    * Phing, Composer, PHPStan, PHPUnit, ...
* Database migrations
    * Which database changes were introduced in a current version
* Security
    * Important security upgrades
* Application
    * Changes in a code that may require some changes in a particular implementation.
