# Shopsys Form Types Bundle

[![Build Status](https://travis-ci.org/shopsys/form-types-bundle.svg?branch=master)](https://travis-ci.org/shopsys/form-types-bundle)
[![Downloads](https://img.shields.io/packagist/dt/shopsys/form-types-bundle.svg)](https://packagist.org/packages/shopsys/form-types-bundle)

Symfony bundle adding form types for usage in [Shopsys Framework](https://www.shopsys-framework.com), its components and plugins.

This repository is maintained by [shopsys/shopsys] monorepo, information about changes is in [monorepo CHANGELOG.md](https://github.com/shopsys/shopsys/blob/master/CHANGELOG.md).

## Installation
The plugin is a Symfony bundle and is installed in the same way:

### Download
First, you download the package using [Composer](https://getcomposer.org/):
```
composer require shopsys/form-types
```

### Register
For the bundle to be loaded in your application you need to register it in the `app/AppKernel.php` file of your project:
```php
// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Shopsys\FormTypesBundle\ShopsysFormTypesBundle(),
            // ...
        ];

        // ...

        return $bundles;
    }

    // ...
}
```

## How to use a custom form type
The form types in this package are regular Symfony form types.
See [Symfony Forms Documentation](https://symfony.com/doc/current/forms.html) for detailed explanation.

## Contents

### [MultidomainType](./src/MultidomainType.php)
Compound type that renders one form of given type for each domain.

It can be configured via the following options:
* `entry_type` - The type of the inner form.
Defaults to `TextType::class`.
* `entry_options` - The options of the inner forms.
Defaults to `[]`.
* `options_by_domain_id` - The options of the inner forms based on the domain ID.
Provide arrays indexed by the domain ID, values are merged with the `entry_options`.
Defaults to `[]`.

The data of the inner forms are returned as an array indexed by the domain ID.

![MultidomainType usage example](./docs/images/multidomain_type_example.png)

### [YesNoType](./src/YesNoType.php)
Natural looking choice type for boolean value inputs.

It has no notable options.

A boolean value is accepted/returned as data.
A null value can be accepted/returned when no radio button is checked.

![YesNoType usage example](./docs/images/yes_no_type_example.png)

## Contributing
Thank you for your contributions to Shopsys Form Types Bundle package.
Together we are making Shopsys Framework better.

This repository is READ-ONLY.
If you want to [report issues](https://github.com/shopsys/shopsys/issues/new) and/or send [pull requests](https://github.com/shopsys/shopsys/compare),
please use the main [Shopsys repository](https://github.com/shopsys/shopsys).

Please, check our [Contribution Guide](https://github.com/shopsys/shopsys/blob/master/CONTRIBUTING.md) before contributing.

## Support
What to do when you are in troubles or need some help? Best way is to contact us on our Slack [http://slack.shopsys-framework.com/](http://slack.shopsys-framework.com/)

If you want to [report issues](https://github.com/shopsys/shopsys/issues/new), please use the main [Shopsys repository](https://github.com/shopsys/shopsys).

[shopsys/shopsys]:(https://github.com/shopsys/shopsys)
