# Start Building Your Application

Installation of the Shopsys Framework is complete and now you can start building your application.
Here are first steps you should start with.

*Note: If you don't have a working application, [install it](/docs/installation/installation-guide.md) first.*

## Commit lock files

Committing lock files is important because the application is then installed in the same way in all environments (most importantly in the production) and also in the same way for all team members.

```bash
git add composer.lock package-lock.json migrations-lock.yml
git commit -m'locked composer, npm and migrations'
```

## Set up domains

A domain can be understood as one instance of the shop.
For example, just furniture can be bough on the domain shopsys-furniture.com while only electronics can be found on the domain shopsys-electro.com.

*Note: Learn more about domain concept fully in [Domain, Multidomain, Multilanguage](/docs/introduction/domain-multidomain-multilanguage.md#domain) article.*

When you install new project, domains are set like this
* `shopsys` on the URL `http://127.0.0.1:8000`
* `2.shopsys` on the URL `http://127.0.0.2:8000`

Read [settings and working with domain](/docs/introduction/how-to-set-up-domains-and-locales.md#settings-and-working-with-domains) to learn how to set your domains correctly. If you have project with only one domain, read [how to create a single domain application](/docs/introduction/how-to-set-up-domains-and-locales.md#1-how-to-create-a-single-domain-application). If you have project with more than two domains, read [how to add a new domain](/docs/introduction/how-to-set-up-domains-and-locales.md#2-how-to-add-a-new-domain).

We highly recommend to set up domains in the beginning of your project correctly. It will save you a lot of time.

*Note: When you change the domain settings, it is likely that [tests](/docs/introduction/automated-testing.md) (mainly [acceptance](/docs/introduction/automated-testing.md#acceptance-tests-aka-functional-tests-or-selenium-tests)) will fail and that [demo data](/docs/introduction/basic-and-demo-data-during-application-installation.md) will not fit you.
It is up to you to adjust them accordingly for your needs.*

*Note 2: If you add a domain, please create and upload an icon for the new domain (Administration > Settings >  E-shop identification). You'll make shop administrators happy.*

## Set up locales

A locale is a combination of language and national settings like a collation of language-specific characters.
We use [ISO 639-1 codes](https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes) to specify locale *(eg. `cs`, `de`, `en`, `sk`)*.
Every domain has defined one locale and also administration has defined its locale.

When you install new project, locales are set like this
* `shopsys` *(1st domain)*: `en`
* `2.shopsys` *(2nd domain)*: `cs`
* administration: `en`

In case you want to change domain locale read [locale settings](/docs/introduction/how-to-set-up-domains-and-locales.md#3-locale-settings) or in case you want to change default administration locale read [locale in administration](/docs/introduction/how-to-set-up-domains-and-locales.md#36-locale-in-administration).

## Set up Elasticsearch

We use Elasticsearch on the frontend for product searching, filtering and for fast listing of products to provide better performance.
You are likely to adjust the Elasticsearch configuration for example if you have a technical shop where the inflection of product names doesn't make sense (we use inflection during searching by default).

*Note: Find more in detailed article about [Elasticsearch](/docs/model/elasticsearch.md) usage.*

Every domain has defined one [Elasticsearch index](/docs/model/elasticsearch.md#elasticsearch-index-setting). Definition of this index can be found in `src/Shopsys/ShopBundle/Resources/definition/<domain_id>.json` files.
The most often change is adding [fields](https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping.html) and changing [analysis](https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis.html) to justify search behavior.

## Set up routing

Routing is a mechanism that maps URL path to controllers and methods.
You are likely to adjust routing when you need translated routes for a new locale (e.g. when you have a domain with German localization, and want to have a list of orders under URL `/befehle`).

*We use Symfony routing, so please find more in the [official documentation](https://symfony.com/doc/3.4/routing.html)*

You can adjust the routing in `src/Shopsys/ShopBundle/Resources/config/routing_front.yml` file and [locale specific](/docs/introduction/how-to-set-up-domains-and-locales.md#32-frontend-routes) in `src/Shopsys/ShopBundle/Resources/config/routing_front_xx.yml` files.

## Set up default currency

A default currency is a currency that is displayed when showing a price in a certain part of the system.
The default currency is different for administration and for each of domains and you can adjust the default currency for each one individually.

The administration default currency is used in twig templates eg. as `{{ value|priceWithCurrencyAdmin }}`.
The default currency for domain is used eg. as `{{ cartItemDiscount.priceWithVat|price }}`.

*Note: Read more in a dedicated article about [price filters](/docs/model/how-to-work-with-money.md#price) and [administration price filter](/docs/model/how-to-work-with-money.md#pricewithcurrencyadmin).*

When you install new project, default currencies are set like this
* `shopsys` *(1st domain)*: `CZK`
* `2.shopsys` *(2nd domain)*: `EUR`
* administration: `CZK`

You can change default currencies in administration `Pricing > Currencies`, but this change will not last after application rebuild (operation that you do often during development).

### How to set default currency permanently

You can adjust the demo data to match your project.
This takes a bit more effort but once you adjust demo data, the change will be applied every time application is rebuilded.

#### How to set administration default currency
Class `SettingValueDataFixture`, method `load()`
```diff
+ $eurCurrency = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);
+ $this->setting->set(PricingSetting::DEFAULT_CURRENCY, $eurCurrency->getId());
```

#### How to set first domain default currency
Class `SettingValueDataFixture`, method `load()`
```diff
+ $eurCurrency = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);
+ $this->setting->setForDomain(PricingSetting::DEFAULT_DOMAIN_CURRENCY, $eurCurrency->getId(), Domain::FIRST_DOMAIN_ID);
```

#### How to set next domains default currency
Class `MultidomainSettingValueDataFixture`, method `load()`
```diff
- $defaultCurrency = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);
+ $defaultCurrency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
```
or you can even use switch logic to provide different default currencies for different domains like
```php
switch ($domainId) {
    case 2: $defaultCurrency = $this->getReference(CurrencyDataFixture::CURRENCY_EUR); break;
    case 3: $defaultCurrency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK); break;
    ...
```
