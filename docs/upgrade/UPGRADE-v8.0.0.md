# Upgrade to v8.0.0

This guide contains instructions to upgrade to v8.0.0.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]

### Application
- constructors of `FrameworkBundle\Model\Mail\Mailer` and `FrameworkBundle\Component\Cron\CronFacade` classes were changed so if you extend them change them accordingly: ([#875](https://github.com/shopsys/shopsys/pull/875)).
    - `CronFacade::__construct(Logger $logger, CronConfig $cronConfig, CronModuleFacade $cronModuleFacade, Mailer $mailer)`
    - `Mailer::__construct(Swift_Mailer $swiftMailer, Swift_Transport $realSwiftTransport)`
    - find all usages of the constructors and fix them
- `EntityNameResolver` was added into constructor of these classes: ([#918](https://github.com/shopsys/shopsys/pull/918))
    - CronModuleFactory
    - PersistentReferenceFactory
    - ImageFactory
    - FriendlyUrlFactory
    - SettingValueFactory
    - UploadedFileFactory
    - AdministratorGridLimitFactory
    - EnabledModuleFactory
    - ProductCategoryDomainFactory
    - ProductVisibilityFactory
    - ScriptFactory
    - SliderItemFactory

    In case of extending one of these classes, you should add an `EntityNameResolver` to a constructor and use it in a `create()` method to resolve correct class to return.

[shopsys/framework]: https://github.com/shopsys/framework