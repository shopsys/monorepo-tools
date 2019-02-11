# How to Set Up Domains and Locales (Languages)

This article describes how to work with domains and languages during the development of your project.
For an explanation of the basic terms, please read [domain, multidomain and multilanguage](domain-multidomain-multilanguage.md) article first.

*Note: Demo data on the Shopsys Framework contains data only in `en` and `cs` locales*

## Settings and working with domains

### 1. How to create a single domain application

#### 1.1 Domain configuration
Modify the configuration of the domain in `app/config/domains.yml`.
This configuration file contains informations about the domain ID, the domain identifier for the domain tabs in the administration, and the domain locale.

#### 1.2 Set up the url address
Set the url address for the domain in `app/config/domains_urls.yml`.

#### 1.3 Set up the application as "singledomain"
Modify the value of the parameter `is-multidomain` in `build.xml` to `false`.
Based on this parameter, smoke and functional tests are run for a single domain, or multiple domains, respectively.

#### 1.4 Locale settings
Set up the locale of the domain according to the instructions in the section [Locale settings](#3-locale-settings)

#### 1.5 Build
Start the build, for example using a phing target
```
php phing build-demo-dev
```
*Note: In this step you were using Phing target `build-demo-dev`.
More information about what Phing targets are and how they work can be found in [Console Commands for Application Management (Phing Targets)](/docs/introduction/console-commands-for-application-management-phing-targets.md)*

***Note:** During the execution of `build-demo-dev phing target`, there will be installed 3-rd party software as dependencies of Shopsys Framework by [composer](https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies) and [npm](https://docs.npmjs.com/about-the-public-npm-registry) with licenses that are described in document [Open Source License Acknowledgements and Third-Party Copyrights](../../open-source-license-acknowledgements-and-third-party-copyrights.md)*

After the build is completed, a singledomain application is created.

#### 1.6 Tests
Some tests are prepared for the configuration with the first domain with `en` locale.
For example `Tests\ShopBundle\Functional\Twig\PriceExtensionTest` is expecting the specific format of displayed currency.
If you want to use already created tests for your specific configuration, you may need to modify these tests to be able to test your specific configuration of the domain.

### 2. How to add a new domain

#### 2.1 Domain configuration
Modify the configuration of the domain in `app/config/domains.yml`.
This configuration file contains pieces of information about the domain ID, the domain identifier for the domain tabs in the administration, and the domain locale.

#### 2.2 Set up the url address
Set the url address for the domain in `app/config/domains_urls.yml`.

*Note: When you add a domain with the new url address on the MacOS platform, you need to enable this url address also in the network interface, see [Installation Using Docker for MacOS](https://github.com/shopsys/shopsys/blob/master/docs/installation/installation-using-docker-macos.md#11-enable-second-domain-optional)*

#### 2.3 Set up the application as "multidomain"
Modify the value of the parameter `is-multidomain` in `build.xml` to `true` (this is the default value).
Based on this parameter, smoke and functional tests are run for a single domain, or multiple domains, respectively.

#### 2.4 Locale settings
Set up the locale of the domain according to the instructions in the section [Locale settings](#3-locale-settings)

#### 2.5 Create multidomains data
There need to be created some multidomain data for the newly added domain.
Run the phing target
```
php phing create-domains-data
```
This command performs multiple actions:
- multidomain attributes from the first domain are copied for this new domain, see `FrameworkBundle/Component/Domain/DomainDataCreator.php`, where the `TEMPLATE_DOMAIN_ID` constant is defined.
- if a new locale is set for the newly added domain, the empty rows with this new locale will be created for multilang attributes
- pricing group with the name Default is created for every new domain
- the last step of this command is the start of automatic recalculations of prices, availabilities, and products visibilities.

#### 2.6 Multilang attributes
Demo data of Shopsys Framework are prepared only for `en` and `cs` locales.
This means that if you are using a different locale, these multilang attributes will be empty for this new locale even after the installation of demo data.

#### 2.7 Generate assets for the new domain
In order to properly display the new domain, assets need to be generated
```
php phing grunt
```

#### 2.8. Create elasticsearch definition for the new domain
The configuration for elasticsearch must be created for each domain in a separate json file.
By default, the configurations for the domain 1 and 2 are already parts of a project-base.
Configuration for elasticsearch can be found in `src/Shopsys/ShopBundle/Resources/Resources/definition/`.
If you add a new domain, you need to create an elasticsearch configuration for this new domain.

### 3. Locale settings
Some parts of these instructions are already prepared for the locales `en` and `cs`.

#### 3.1 Set up the locale for domain
Set up the locale of the domain in `app/config/domains.yml`.
This configuration file contains pieces of information about the domain ID, the domain identifier for the domain tabs in the administration, and the domain locale.

#### 3.2 Frontend routes
Create a file with the frontend routes for the added locale if this file is not already created for this locale.
Create this file in the directory `ShopBundle/Resources/config/` with the name `routing_front_xx.yml` where `xx` replace for the code of added locale.
Import the new routes configuration in `app/config/packages/shopsys_shop.yml`

#### 3.3 Translations and messages
In order to correctly display the labels like *Registration*, *Cart*, ..., create a file with translations of messages in `ShopBundle/Resources/translations/`.
Modify the phing target `dump-translations-project-base` in `build-dev.xml` by adding the new locale as `<arg value="xx" />` where `xx` replace for the code of added locale.
Then run
```
php phing dump-translations
```
There will be created files for translations of messages for the new locale in `ShopBundle/Resources/translations/`.

For more information about translations, see [the separate article](/docs/introduction/translations.md).

#### 3.4 Generate database functions for the locale use
Within the database functions, it is necessary to regenerate the default database functions for the locale use that are already created for the `en` locale as default.
Regenerate database functions by running a phing target
```
php phing create-domains-db-functions
```

#### 3.5 Multilang attributes
Demo data of Shopsys Framework are prepared only for `en` and `cs` locales.
This means that if you are using a different locale, these multilang attributes will be empty for this new locale even after the installation of demo data.

#### 3.6 Locale in administration
Administration is by default in `en` locale.
This means that for example product list in administration tries to display translations of product names in `en` locale.
If you want to switch it to the another locale, set a parameter `shopsys.admin_locale` in your `parameters.yml` configuration to desired locale.
However, the selected locale has to be one of registered domains locale.

### 4. Change the url address for an existing domain

#### 4.1 Change the url address
Change the url address in the configuration of the domain in `app/config/domains_urls.yml`.

*Note: When you add a domain with the new url address on the MacOS platform, you need to enable this url address also in the network interface, see [Installation Using Docker for MacOS](https://github.com/shopsys/shopsys/blob/master/docs/installation/installation-using-docker-macos.md#11-enable-second-domain-optional)*

#### 4.2 Replace the old url address
Run the phing target
```
php phing replace-domains-urls
```
Running this command will ensure replacing all occurrences of the old url address in the text attributes in the database with the new url address.

### 5. Change the locale for an existing domain
This scenario is not supported by default because of the fact, that change of the locale within an already running eshop almost never happens.
However, there is workaround even for this scenario.

#### Change the locale to the locale that is already used by another domain
If you need to change the locale of a specific domain to another locale that is already used by another domain, just set the required locale for this domain in the `app/config/domains.yml`.

#### Change the locale to the locale that is not yet used by another domain
If you need to change the locale of a specific domain to another locale that is not yet already used by another domain, add new temporary domain with this new locale and follow the instructions of [How to add a new domain](#2-how-to-add-a-new-domain).
The following procedure is the same as in the case with [Change the locale to the locale that is already used by another domain](#change-the-locale-to-the-locale-that-is-already-used-by-another-domain).
