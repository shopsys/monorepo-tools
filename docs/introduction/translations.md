# Translations

Translating is a process of extracting locale-specific texts from your application and converting them into a target language.

We use standard [Symfony translation](https://symfony.com/doc/current/translation.html) so you can use all standard features.
In this article, we describe tools and recommendations for translations.

## Usage

1. Use a translator for your [translatable texts](#translatable-texts). Shopsys Framework implementation of a translator is [`Shopsys\FrameworkBundle\Component\Translation\Translator`](/packages/framework/src/Component/Translation/Translator.php) class.
   You can find more in [Symfony translation documentation](https://symfony.com/doc/current/components/translation/usage.html).

1. Once you have translations in your code, you have to extract them by running `php phing dump-translations`.
   This command extracts the translatable texts into `*.po` translation files located in directory [src/Shopsys/ShopBundle/Resources/translations/](/project-base/src/Shopsys/ShopBundle/Resources/translations/).
   For more information about phing targets in general, see the [separate article](/docs/introduction/console-commands-for-application-management-phing-targets.md).

1. Now you have to translate the newly extracted texts.
   The `*.po` files are text files so you can make translations in a text editor or you can use specialized software.
   It is a good practice to version the files along with source codes so they are a part of your project history.
   If you need more information about the `*.po` format, please read more in the [documentation](https://docs.transifex.com/formats/gettext).

1. Once you create new translations in the `*.po` files, the application will use these translations immediately.

## Message ID

The message ID is the string you put into translation function. In the case of `{{ 'Cart'|trans }}`, the message ID is `Cart`.

We use the original English form as the ID. So in the case of
```twig
{% trans with {'%price%': remainingPriceWithVat|price} %}
    You still have to purchase products for <strong> %price% </strong> for <strong> free </strong> shipping and payment.
{% endtrans %}
```
the message ID is `You still have to purchase products for <strong> %price% </strong> for <strong> free </strong> shipping and payment.`.

We replace multiple spaces in message ID to a single one. So in case of
```twig
{% trans %}
    Shipping and payment
    <strong>for free!</strong>
{% endtrans %}
```
the message ID is `Shipping and payment <strong>for free!</strong>`.

Never use variables in message IDs directly. Extractor is not able to guess what is in the variable. Use placeholders instead.
```diff
$translator->trans(
-    'Thanks to ' . $name
+    'Thanks to %name%',
+    ['%name%' => $name]
);
```

This results in message ID `Thanks to %name%` that can be translated even with different word order, for example `%name%, danke!`.

From time to time we use word classes in message ID, for example `order [noun]`, `order [verb]` that are both translated as `order`.
We do this because in Czech, the noun is translated as *"objednávka"* and the verb is translated as *"objednat"*.

## Translatable texts

When `php phing dump-translations` command is run, texts are extracted from following places:

### PHP

```php
$this->translator->trans('Offer in feed');

$this->translator->transChoice('{0} no products|{1} product|]1,Inf[ products', $count);

// shortcut for Translator::staticTrans()
t('Offer in feed');

// shortcut for Translator::staticTransChoice()
tc('{0} no products|{1} product|]1,Inf[ products', $count);

// see Shopsys\FrameworkBundle\Component\TranslationConstraintViolationExtractor
$executionContextInterface->addViolation('This message will be extracted into "validators" translation domain');

// see Shopsys\FrameworkBundle\Component\Translation\ConstraintMessageExtractor
new Constraint\Length([
    'message' => 'This message will be extracted into "validators" translation domain',
    'minMessage' => 'Actually, every option ending with "message" will be extracted',
]);

// see Shopsys\FrameworkBundle\Component\Translation\ConstraintMessagePropertyExtractor
class MyConstraint extends \Symfony\Component\Validator\Constraint
{
    public $message = 'This value will be extracted.';
    public $otherMessage = 'This value will also be extracted.';
}
```

### Twig

```twig
{{ 'Add another parameter'|trans }}

{% trans %}Add another parameter{% endtrans %}

{{ '{0} no products|{1} product|]1,Inf[ products'|transchoice(count) }}

{% transchoice count  %}
    {0} no products|{1} product|]1,Inf[ products
{% endtranschoice %}

{{ 'items added to <a href="/cart">cart</a>'|transHtml }}

{{ '{1} item added to <a href="/cart">cart</a>|]1,Inf[ items added to <a href="/cart">cart</a>'|transchoiceHtml(count) }}
```

`trans` and `transchoice` are standard Symfony translations.
`transHtml` and `transchoiceHtml` are our custom translation methods that can be used only in Twig templates and are similar to filters `|trans|raw`.
The difference is that `transHtml` and `transchoiceHtml` escape parameters to prevent XSS.

They are safe to use in a place where you need HTML in texts together with parameters that are taken from user input.

A usage for example:
```twig
{{ 'You have to <a href="%url%">choose</a> products'|transHtml({ '%url%': url('front_homepage') }) }}
```

_Note: The message is not escaped, so if there is malicious code in `*.po` files, it will not be escaped._

### JavaScript

```js
Shopsys.translator.trans('Please enter discount code.');

Shopsys.translator.transChoice('{1}Load next item|]1,Inf[Load next items', loadNextCount);
```

JavaScript translations are extracted and translated during compilation of JavaScript.

## Possible ways of changing translations

* You want to keep the message ID same as the original English form
    * change the text in the code
    * run `php phing dump-translations`
    * translate text again in `*.po` file(s) for the other languages because the message ID changed
    * be careful, this is a backward compatibility breaking change because the original message ID does not exist anymore.
* You do not care about the consistency between message ID and the original English form
    * just change the translations in `*.po` file(s) for English and all the other languages

## Localized routes
On Shopsys Framework, you can translate URLs of your routes.
See ["Frontend routes" section in "How to Set Up Domains and Locales" article](/docs/introduction/how-to-set-up-domains-and-locales.md#32-frontend-routes) for more information.
You can see the list of all translated URLs in administration page `admin/superadmin/urls/` (you must be logged in as a superadmin).
