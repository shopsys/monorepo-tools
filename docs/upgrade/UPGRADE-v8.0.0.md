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

[shopsys/framework]: https://github.com/shopsys/framework