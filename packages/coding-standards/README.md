# ShopSys Coding Standard

[![Build Status](https://travis-ci.org/shopsys/coding-standards.svg?branch=master)](https://travis-ci.org/shopsys/coding-standards)
[![Downloads](https://img.shields.io/packagist/dt/shopsys/coding-standards.svg)](https://packagist.org/packages/shopsys/coding-standards)

This project provides coding standards for ShopSys projects.


It contains **few extra ShopSys rules** to those defaults ones:


### Rules for [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

`Shopsys/missing_button_type` 

- Adds mandatory `type` attribute to `<button>` HTML tag.


`Shopsys/orm_join_column_require_nullable`    

- Doctrine annotations `@ORM\ManyToOne` and `@ORM\OneToOne` must have defined `nullable` option in `@ORM\JoinColumn`.


`Shopsys/no_unused_imports`                   

- Unused use statements (except those from the same namespace) must be removed.


### Rules for [PHPMD](https://github.com/phpmd/phpmd)

`CamelCasePropertyName`
                             
- Property names must be in camelCase                                                                                                



## Install

```bash
php composer require shopsys/coding-standards
```


## Usage

For further information see official documentation of particular project.

### [PHP-Parallel-Lint](https://github.com/JakubOnderka/PHP-Parallel-Lint)

```bash
php vendor/bin/parallel-lint /path/to/project
```

### [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

```bash
php vendor/bin/php-cs-fixer fix /path/to/project --config=vendor/shopsys/coding-standards/build/phpcs-fixer.php_cs
```

### [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)

```bash
php vendor/bin/phpcs /path/to/project --standard=vendor/shopsys/coding-standards/rulesetCS.xml
```

### [PHPMD](https://github.com/phpmd/phpmd)

```bash
php vendor/bin/phpmd /path/to/project text vendor/shopsys/coding-standards/rulesetMD.xml
```
