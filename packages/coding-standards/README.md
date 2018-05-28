# Shopsys Coding Standards

[![Build Status](https://travis-ci.org/shopsys/coding-standards.svg?branch=master)](https://travis-ci.org/shopsys/coding-standards)
[![Downloads](https://img.shields.io/packagist/dt/shopsys/coding-standards.svg)](https://packagist.org/packages/shopsys/coding-standards)

Shopsys Coding Standards are based on [PSR-2](http://www.php-fig.org/psr/psr-2/).

This project bundles tools along with predefined rulesets for automated checks of Shopsys Coding Standards that we use in many Shopsys projects.
The repository also contains [few custom rules](#custom-rules).

This repository is maintained by [shopsys/shopsys] monorepo, information about changes are in [monorepo CHANGELOG.md](https://github.com/shopsys/shopsys/blob/master/CHANGELOG.md).

Provided tools:

* [PHP-Parallel-Lint](https://github.com/JakubOnderka/PHP-Parallel-Lint)
* [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard) that combines [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) and [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)

For further information see official documentation of those tools.

## Installation

```bash
php composer require shopsys/coding-standards
```

## Usage

Create `custom-coding-standard.yml` config file in your project which includes predefined rulesets. 
You can also [customize the rules](./UPGRADE.md) 
and even add your own sniffs and fixers in the config.

```yaml
#custom-coding-standard.yml
imports:
   - { resource: '%vendor_dir%/symplify/easy-coding-standard/config/psr2.yml' }
   - { resource: '%vendor_dir%/shopsys/coding-standards/shopsys-coding-standard.yml' }

```

In terminal, run following commands:

```bash
php vendor/bin/parallel-lint /path/to/project
php vendor/bin/ecs check /path/to/project --config=/path/to/project/custom-coding-standard.yml
```

## Custom rules

### Rules for [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

#### `Shopsys/missing_button_type`

All `<button>` HTML tags in `.html` and `.html.twig` files must have explicit `type` attribute.

If the `type` is not specified it will be fixed to `type="button""` because the implicit value is `submit` which makes it behave differently based on the context (`<button>` inside `<form>` element submits the form).

#### `Shopsys/orm_join_column_require_nullable`    

Doctrine annotations `@ORM\ManyToOne` and `@ORM\OneToOne` must have `nullable` option defined explicitly in `@ORM\JoinColumn`.

If the `nullable` option is not specified it will be fixed to `nullable=false` because the implicit value is `true` but this is the opposite to the implicit value of `nullable` for `@Column` annotation.
This makes it consistent.

### Rules for [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)

#### `ForbiddenExitSniff`

Function `exit()` is not allowed.

#### `ForbiddenSuperGlobalSniff`

Usage of superglobals (`$_COOKIE`, `$_GET`, `$_FILES`, `$_POST`, `$_REQUEST`, `$_SERVER`) is not allowed.

#### `ValidVariableNameSniff`

Default `\PHP_CodeSniffer\Standards\Squiz\Sniffs\NamingConventions\ValidVariableNameSniff`
does not report method parameters in `$_var` format as an violation but it should. 
It also skips checking of private members when `PrivateNoUnderscore` property is disabled.

This sniff provides the missing functionality and is intended to be used as an addition to the default `ValidVariableNameSniff`.

## Contributing
Thank you for your contributions to Shopsys Coding Standards package.
Together we are making Shopsys Framework better.

This repository is READ-ONLY.
If you want to [report issues](https://github.com/shopsys/shopsys/issues/new) and/or send [pull requests](https://github.com/shopsys/shopsys/compare),
please use the main [Shopsys repository](https://github.com/shopsys/shopsys).

Please, check our [Contribution Guide](https://github.com/shopsys/shopsys/blob/master/CONTRIBUTING.md) before contributing.

## Support
What to do when you are in troubles or need some help? Best way is to contact us on our Slack [http://slack.shopsys-framework.com/](http://slack.shopsys-framework.com/)

If you want to [report issues](https://github.com/shopsys/shopsys/issues/new), please use the main [Shopsys repository](https://github.com/shopsys/shopsys).

[shopsys/shopsys]:(https://github.com/shopsys/shopsys)
