# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Unreleased
### Added
- extracted core functionality of [Shopsys Framework](http://www.shopsys-framework.com/)
from its open-box repository [shopsys/project-base](https://github.com/shopsys/project-base) (@MattCzerner)
    - this will allow the core to be upgraded via `composer update` in different project implementations
    - core functionality includes:
        - all Shopsys-specific Symfony commands
        - model and components with business logic and their data fixtures
        - Symfony controllers with form definitions, Twig templates and all javascripts of the web-based administration
        - custom form types, form extensions and twig extensions
        - compiler passes to allow basic extensibility with plugins (eg. product feeds)
    - this is going to be a base of a newly built architecture of [Shopsys Framework](http://www.shopsys-framework.com/)
- styles related to admin extracted from [shopsys/project-base](https://github.com/shopsys/project-base) package (@MattCzerner)
    - this will allow styles to be upgraded via `composer update` in project implementations
