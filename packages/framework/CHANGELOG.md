# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- Countries have code in ISO 3166-1 alpha-2 (@petr.kadlec)
- admin: added site content and email template for personal data export (@stanoMilan)
- extended glass-box model entities are now used instead of their parent entities in EntityManager and QueryBuilders (@PetrHeinz, @vitek-rostislav)
    - this removes the need to manually override all repositories that work with extended entities
    - the functionality is automatically tested in [shopsys/project-base](https://github.com/shopsys/project-base)
        - see `\Tests\ShopBundle\Database\EntityExtension\EntityExtensionTest`
- entities are created by factories (@simara-svatopluk)
    - allowing override factory that creates extended entities in project-base
- admin: shipping detail: payment now can be assigned to shipping (@TomasLudvik, @boris-brtan)

### Changed
- visibility of all private properties and methods of repositories of entities was changed to protected (@Miroslav-Stopka)
    - there are changed only repositories of entities because currently there was no need for extendibility of other repositories
    - protected visibility allows overriding of behavior from projects
- visibility of all private properties and methods of DataFactories was changed to protected (@Miroslav-Stopka)
    - protected visibility allows overriding of behavior from projects
- unification of terminology - indices and indexes (@Miroslav-Stopka)
    - there is only "indexes" expression used now
- `CustomerFormType`, `PaymentFormType` and `TransportFormType` are now all rendered using FormType classes and they
    are ready for extension from `project-base` side. (@MattCzerner)
- moved constants with types of environment from [shopsys/project-base](https://github.com/shopsys/project-base) (@PetrHeinz)
    - moved from `\Shopsys\Environment` to `\Shopsys\FrameworkBundle\Component\Environment\EnvironmentType`
- service definition follows Symfony 4 autowiring standards (@EdoBarnas)
    - FQN is always used as service ID
    - usage of interfaces is preferred, if possible
    - all services are explicitly defined
        - services with common suffixes (`*Facade`, `*Repository` etc.) are auto-discovered
        - see `services.yml` for details
- all exception interfaces are now Throwable (@TomasLudvik)
- visibility of all private properties and methods of facades was changed to protected (@vitek-rostislav)
    - protected visibility allows overriding of behavior from projects

### Fixed
- choiceList values are prepared for js Choice(s)ToBooleanArrayTransformer (@Miroslav-Stopka)
    - fixed "The choices were not found" console js error in the params filter
- command `shopsys:server:run` for running PHP built-in web server for a chosen domain (@TomasLudvik)
- db indices for product name are now created for translations in all locales (@vitek-rostislav)
- `LoadDataFixturesCommand` - fixed the `--fixtures` option description (@vitek-rostislav)

## 7.0.0-alpha1 - 2018-04-12
- We are releasing the Shopsys Framework in version 7 and we are synchronizing versions because
  the Shopsys Framework and all packages are now developed together and are now same-version compatible.

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
- glass-box model entities are now extensible from project-base without changing the framework code (@simara-svatopluk)
    - the entity extension is a work in progress
    - currently it would require you to override a lot of classes to use the extended entities instead of the parents
- [Shopsys Community License](./LICENSE)

### Changed
- configuration of form types in administration is enabled using form type options (@Miroslav-Stopka)
    -  following form types configured using options:
        - VatSettingsFormType
        - SliderItemFormType
        - ShopInfoSettingFormType
        - SeoSettingFormType
        - MailSettingFormType
        - LegalConditionsSettingFormType
        - HeurekaShopCertificationFormType
        - CustomerCommunicationFormType
        - CookiesSettingFormType
        - CategoryFormType
        - ArticleFormType
        - AdvertFormType
        - AdministratorFormType

[Unreleased]: https://github.com/shopsys/framework/compare/v7.0.0-alpha1...HEAD
