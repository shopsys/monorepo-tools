# Shopsys Coding Standards

[![Build Status](https://travis-ci.org/shopsys/coding-standards.svg?branch=master)](https://travis-ci.org/shopsys/coding-standards)
[![Downloads](https://img.shields.io/packagist/dt/shopsys/coding-standards.svg)](https://packagist.org/packages/shopsys/coding-standards)

Shopsys Coding Standards are based on [PSR-2](http://www.php-fig.org/psr/psr-2/).

This project bundles tools along with predefined rulesets for automated checks of Shopsys Coding Standards that we use in many Shopsys projects.
The repository also contains [few custom rules](#custom-rules).

Provided tools:
* [PHP-Parallel-Lint](https://github.com/JakubOnderka/PHP-Parallel-Lint)
* [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)
* [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)
* [PHPMD](https://github.com/phpmd/phpmd)

For further information see official documentation of those tools.

## Installation

```bash
php composer require shopsys/coding-standards
```

## Usage
```bash
php vendor/bin/parallel-lint /path/to/project
php vendor/bin/php-cs-fixer fix /path/to/project --config=vendor/shopsys/coding-standards/build/phpcs-fixer.php_cs
php vendor/bin/phpcs /path/to/project --standard=vendor/shopsys/coding-standards/rulesetCS.xml
php vendor/bin/phpmd /path/to/project text vendor/shopsys/coding-standards/rulesetMD.xml
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
