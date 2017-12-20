# ShopSys Coding Standard

[![Build Status](https://travis-ci.org/shopsys/coding-standards.svg?branch=master)](https://travis-ci.org/shopsys/coding-standards)
[![Downloads](https://img.shields.io/packagist/dt/shopsys/coding-standards.svg)](https://packagist.org/packages/shopsys/coding-standards)

This project provides coding standards for ShopSys projects.


## Install

```
php composer require shopsys/coding-standards
```


## Usage

For further information see official documentation of particular project.

### [PHP-Parallel-Lint](https://github.com/JakubOnderka/PHP-Parallel-Lint)

```
php vendor/bin/parallel-lint /path/to/project
```

### [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

```
php vendor/bin/php-cs-fixer fix /path/to/project --config=vendor/shopsys/coding-standards/build/phpcs-fixer.php_cs
```

### [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)

```
php vendor/bin/phpcs /path/to/project --standard=vendor/shopsys/coding-standards/rulesetCS.xml
```

### [PHPMD](https://github.com/phpmd/phpmd)

```
php vendor/bin/phpmd /path/to/project text vendor/shopsys/coding-standards/rulesetMD.xml
```
