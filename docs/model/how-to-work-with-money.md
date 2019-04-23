# How to Work with Money

Money is a very important concept for every e-commerce project.
In Shopsys Framework, all monetary values (*prices, account balances, discount amounts, price limits etc.*) are represented by an instance of [the `Money` class](#money-class).

This approach has several advantages:
- it avoids problems with floating point number calculations and comparisons (see [official PHP documentation](http://php.net/manual/en/language.types.float.php) for details)
- allows easy-to-use interfaces with consistent type-hinting so you can be sure what type of value you should be using
- prevents accidental conversion to unexpected types (which may be problematic eg. when using the `===` operator)
- makes the application design clearer and future changes easier

**Table of Contents:**
- [General Concept](#general-concept)
- [Money Class](#money-class)
- [Money in Forms](#money-in-forms)
- [Money in Twig Templates](#money-in-twig-templates)
- [Money in Javascript](#money-in-javascript)
- [Money in Doctrine](#money-in-doctrine)
- [Unit and Functional Tests](#unit-and-functional-tests)
- [Price Class](#price-class)

## General Concept
The money concept in Shopsys Framework represents and encapsulates monetary values with a decimal part, like `100`, `0.50`, `10.99`, `0.0005`, ...
Money is represented without currency.

### Scale
Scale defines the precision of the decimal part and it can be a bit tricky.

Imagine you want to represent `1/3` (one third) in your application. In a `float`, it would be actually represented as `0.333333333333333314829616256247390992939472198486328125` because of [the floating-point precision](http://php.net/manual/en/language.types.float.php).

When you want to work with one third in terms of money, you have to specify the scale - the number of places after the decimal point that should be taken into account.
So you can create a monetary value from `1/3` in the scale of 2 (`0.33`), or in the scale of 8 (`0.33333333`).
But it will never be exactly one third as it is inexpressible using a finite decimal.

The money concept keeps the computation and comparisons precise up to the defined scale.
Eg. if you have `1/3` with the scale of 4 (`0.3333`) and multiply it by `3` you'll get `0.9999`, not `1`.
You can get around it using [rounding](#rounding).

The scale has to be specified during [rounding](#rounding), [creating from floats](#construction) and [division](#computation).

## Money Class

[`Money`](/packages/framework/src/Component/Money/Money.php) is an immutable [value object](https://codete.com/blog/value-objects/).

It uses a decimal representation of the money amount and it does not contain any reference to the used currency.
You can get the decimal representation as a `string` via the `getAmount` method.

*Note: If in doubt about the results of any method, you can take a look at [its unit tests](/packages/framework/tests/Unit/Component/Money/MoneyTest.php) which contain many examples of the class' behavior.*

### Construction

`Money` can be constructed directly from integers and numeric strings as they are able to represent decimal numbers precisely: `$tenDollars = Money::create(10)`, `$oneAndHalfEuro = Money::create('1.50')`.

It may be also constructed from floating point numbers, but the scale (number of decimal places) must be explicitly specified: `Money::createFromFloat($floatFromExternalSource, 2)`.
The created value will be [rounded](#rounding) to the provided scale.

Zero amount of money can be constructed by calling `Money::zero()`.

### Computation

To compute with monetary values you have to use the object's methods instead of arithmetic operators (`+`, `-`, `*`, `/`):

- `Money::add(Money $addend) : Money`
- `Money::subtract(Money $subtrahend) : Money`
- `Money::multiply(int|string $multiplier) : Money`
- `Money::divide(int|string $divisor, int $scale) : Money`

*Note: `Money` is immutable, which means that all these methods create a new object and the original is never modified.*

For addition and subtraction, the other parameter has to be also a `Money` instance.
For multiplication and division, the other parameter has to be an integer or a numeric string (as they are able to represent decimal numbers precisely), not a float.

The scale (number of decimal places) of the result is assigned automatically to all operations except division, keeping the results as precise as possible.
Results of a division may be inexpressible with a finite decimal (eg. 1 / 3 = 0.3333...), so it's up to the user to specify the requested scale.
- scale of the result of `add` and `subtract` is the *maximal scale* of both money values
- scale of the result of `multiply` is the *sum of scales* of both money values
- scale of the result of `divide` must be *explicitly specified*, the last decimal place will be rounded to minimize the error

*Note: The scale of the money amount is always preserved - `getAmount` will use all decimal places of its scale (eg. zero money with scale 6 would return `0.000000`).*

### Rounding

You may use `Money::round(int $scale) : Money` method that rounds the amount of money up to `$scale` decimal places, it rounds 0.5 away from zero (making 1.5 into 2 and -1.5 into -2).
This behavior is consistent with `PHP_ROUND_HALF_UP` rounding mode, which is the default mode for [the `round` function](http://php.net/manual/en/function.round.php).

The scale of the result will always be equal to the provided `$scale`.

### Comparing

To compare two monetary values you have to use the object's methods instead of [comparison operators](http://php.net/manual/en/language.operators.comparison.php):

- `Money::equals(Money $other) : bool` instead of `===` and `==`
- `Money::isLessThan(Money $other) : bool` instead of `<`
- `Money::isGreaterThan(Money $other) : bool` instead of `>`
- `Money::isLessThanOrEqualTo(Money $other) : bool` instead of `<=`
- `Money::isGreaterThanOrEqualTo(Money $other) : bool` instead of `>=`
- `Money::compare(Money $other) : int` instead of the Spaceship operator `<=>`

To compare a value with zero you may use the short-hand methods`isPositive`, `isNegative` or `isZero`.
A zero is considered neither positive nor negative.

All methods compare the amounts of both objects counting all decimal places.

## Money in Forms

For the user input of monetary values use the `MoneyType` (see [Symfony docs](https://symfony.com/doc/3.4/reference/forms/types/money.html) for details).

```php
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Validator\Constraints\NotBlank;

// ...

$orderItemFormBuilder->add('priceWithVat', MoneyType::class, [
    'scale' => 6,
    'constraints' => [
        new NotBlank(['message' => 'Please enter unit price with VAT']),
    ],
]);
```

The form type is configured with a model data transformer that converts the value into a `Money` object automatically ([`NumericToMoneyTransformer`](/packages/framework/src/Form/Transformers/NumericToMoneyTransformer.php)).
Thanks to this approach you can use `Money` in your [data objects](/docs/model/entities.md#entity-data) directly.

In Shopsys Framework, the default value of the `currency` option is `false` instead of `EUR`, hiding the currency symbol by default.

*Note: For non-monetary numeric values use `NumberType` (see [Symfony docs](https://symfony.com/doc/3.4/reference/forms/types/number.html) for details).*

### Form Constraints

There are two form constraints to be used specifically with the `MoneyType` form fields.
Because of model data transformation, you cannot use constraints that are validating scalar values (eg. `GreaterThan`).

#### NotNegativeMoneyAmount

Validates that the amount of money is greater or equal to zero.

It has only the `message` option specifying the validation error message in case the entered value is negative.
Specifying the validation message is optional, there is a default value.

```php
use Shopsys\FrameworkBundle\Form\Constraints\NotNegativeMoneyAmount;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Validator\Constraints\NotBlank;

// ...

$priceTableFormBuilder->add($key, MoneyType::class, [
    'scale' => 6,
    'required' => true,
    'invalid_message' => 'Please enter price in correct format (a number with decimal separator)',
    'constraints' => [
        new NotBlank(['message' => 'Please enter price']),
        new NotNegativeMoneyAmount(['message' => 'Price must be greater or equal to zero']),
    ],
]);
```

#### MoneyRange

Similarly to [the Symfony `Range` constraint](https://symfony.com/doc/3.4/reference/constraints/Range.html), it validates that the amount of money is between some minimum and maximum.

It has four options:
- `min` specifies the minimum value, has to be an instance of `Money` or `null`
- `max` specifies the maximum value, has to be an instance of `Money` or `null`
- `minMessage` specifies the validation error message in case the entered value is less than the `min` value
- `maxMessage` specifies the validation error message in case the entered value is greater than the `max` value

At least one of the `min` and `max` options has to be provided for the constraint to make sense.
Specifying the validation messages is optional, there are default values.

```php
use Shopsys\FormTypesBundle\MultidomainType;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Form\Constraints\MoneyRange;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

// ...

$zboziFeedProductFormBuilder->add('cpc', MultidomainType::class, [
    'label' => $this->translator->trans('Maximum price per click'),
    'entry_type' => MoneyType::class,
    'required' => false,
    'entry_options' => [
        'currency' => 'CZK',
        'constraints' => [
            new MoneyRange([
                'min' => Money::create(1),
                'max' => Money::create(500),
            ]),
        ],
    ],
]);
```

## Money in Twig Templates

Instances of the `Money` class cannot be directly converted to strings.
To be able to display a monetary value in a template you should use one of the prepared Twig filters.

These filters can be used only with an instance of `Money`.

### price

Filter `price` formats the amount of money in a localized manner, including the currency symbol.
It uses basic frontend currency on the current domain and the current locale (language) to format it.

For example, one thousand Czech crowns would be rendered as *"CZK1,000.00"* on an English domain and as *"1 000,00 Kč"* on a Czech one.

The filter expects the value to be already rounded.
If not, it will render the extra decimal places.

All `price*` Twig filters use this format.
They usually differ only in the currency and locale they use.

### priceText

Filter `priceText` formats the amount of money in a localized manner, similarly to the `price` filter.
The only difference is that it outputs the text *"Free"* (or the corresponding [translation](/docs/introduction/translations.md)) when zero amount of money is provided.

### priceTextWithCurrencyByCurrencyIdAndLocale

Filter `priceTextWithCurrencyByCurrencyIdAndLocale` can be used to format the amount of money in any currency and locale.
It outputs the text *"Free"* when zero amount of money is provided.

The *currency ID* (`int`) and the *locale* (`string`) must be provided as parameters.

### priceWithCurrency

Filter `priceWithCurrency` can be used to format the amount of money in any currency.
It uses the current locale (the locale of current domain or administration) to format it.

The *currency* (`Currency` entity) must be provided as a parameter.

### priceWithCurrencyAdmin

It works similarly to `priceWithCurrency`, but it uses the default administration currency.

It has no parameters.

### priceWithCurrencyByCurrencyId

It works similarly to `priceWithCurrency`, but it uses currency ID instead of the `Currency` entity.

The *currency ID* (`int`) must be provided as a parameter.

### priceWithCurrencyByDomainId

It works similarly to `priceWithCurrency`, but it uses the basic frontend currency of the provided domain.

The *domain ID* (`int`) must be provided as a parameter.

### moneyFormat

Formats the amount of money as a decimal number without any currency symbol.

Three optional parameters can be provided:
- *number of decimal places* - `null` by default (meaning all), it will [round](#rounding) the value if necessary
- *decimal point character* - `"."` by default
- *separator of thousands* - `""` by default

```twig
{# the "money" variable contains Money::create('1234.5670') #}

{{ money|moneyFormat }}                  {# renders "1234.5670" #}
{{ money|moneyFormat(0) }}               {# renders "1235" #}
{{ money|moneyFormat(2, ',') }}          {# renders "1234,57" #}
{{ money|moneyFormat(null, '.', ',') }}  {# renders "1,234.5670" #}
```

## Money in Javascript

`Money` implements the `JsonSerializable` interface, which means it can be serialized into JSON format with `json_encode` into an object with the following structure:

```json
{
  "amount": "1234.5670"
}
```

This format can be readily used in Javascript when JSON is rendered in Twig:

```twig
{# the "money" variable contains Money::create('1234.5670') #}

<div id="my-money" data-money="{{ money|json_encode }}"/>
```

```js
(function ($) {

    $(document).ready(function () {
        var money = $('#my-money').data('money');

        // Displays "1234.5670"
        alert(money.amount);
    });

})(jQuery);
```

See the native Javascript method [`.toFixed()`](https://www.w3schools.com/jsref/jsref_tofixed.asp) and the function [`parseFloat()`](https://www.w3schools.com/jsref/jsref_parsefloat.asp) to see how to convert between decimal strings and numbers.

## Money in Doctrine

In Shopsys Framework, there is a custom Doctrine type `money` which can be used in similar fashion as `decimal` type.
The entity property value will be automatically hydrated to an instance of `Money` if it's configured to use the type:

```php
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MyEntity
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money|null
     *
     * @ORM\Column(type="money", precision=20, scale=6, nullable=true)
     */
    protected $price;

    // ...
}
```

### In Parameters

When you want to use a `Money` instance as a parameter in DQL, use the `getAmount` method in your [repository class](/docs/model/introduction-to-model-architecture.md#repository):

```php
use Shopsys\FrameworkBundle\Component\Money\Money;

// ...

$priceLimit = Money::create(1000);

/** @var \Doctrine\ORM\QueryBuilder $builder */
$builder->setParameter('priceLimit', $priceLimit->getAmount());
```

### In Function Results

Doctrine hydrates values to an instance of `Money` when selecting an entity property:

```php
use Shopsys\FrameworkBundle\Model\Order\Order;

/** @var \Doctrine\ORM\EntityManager $em */
$result = $em->createQuery('SELECT o.totalPriceWithVat FROM ' . Order::class . ' o WHERE o.id = :id')
    ->setParameter('id', 1)
    ->getSingleResult();

// The result is automatically hydrated into Money
$money = $result['totalPriceWithVat'];
```

But when you're working with aggregate functions (such as `MIN()`, `MAX()`, `SUM()`, etc.) Doctrine cannot infer the type.
In such a case, you have to convert the fetched decimal string into a `Money` instance yourself using `Money::create()`:

```php
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Order\Order;

/** @var \Doctrine\ORM\EntityManager $em */
$result = $em->createQuery('SELECT AVG(o.totalPriceWithVat) AS averagePriceWithVat FROM ' . Order::class . ' o')
    ->getSingleResult();

// The result of a function is a decimal string (eg. '19590.772727272727') and it must be converted manually
$money = Money::create($result['averagePriceWithVat']);
```

## Unit and Functional Tests

You should use `Money` for all monetary values even in tests.

Create the instances via `Money::create(int|string)` in your data providers or directly in your test methods if you don't use providers.

You can use a custom PHPUnit constraint `IsMoneyEqual` for an assertion that two monetary values are equal using the `assertThat($value, Constraint $constraint)` method.

Example:

```php
use Shopsys\FrameworkBundle\Component\Money\Money;
use Tests\FrameworkBundle\Test\IsMoneyEqual;
use Tests\ShopBundle\Test\FunctionalTestCase;

class MyTest extends FunctionalTestCase
{
    // ...

    /**
     * @return array
     */
    public function customerLoyaltyCreditAmountProvider(): array
    {
        return [
            [self::CUSTOMER_ID_WITHOUT_LOYALTY_CREDIT, Money::zero()],
            [self::CUSTOMER_ID_WITH_LOW_LOYALTY_CREDIT, Money::create('12.5')],
            [self::CUSTOMER_ID_WITH_HIGH_LOYALTY_CREDIT, Money::create(1000)],
        ];
    }

    /**
     * @dataProvider customerLoyaltyCreditAmountProvider
     * @param int $customerId
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $expectedCreditAmount
     */
    public function testCustomerLoyaltyCreditAmount(int $customerId, Money $expectedCreditAmount) {
        $customer = $this->getCustomerFromDatabase($customerId);

        $creditAmount = $this->customerLoyaltyFacade->calculateCreditAmount($customer);

        $this->assertThat($creditAmount, new IsMoneyEqual($expectedCreditAmount));
    }
}
```

## Price Class

[`Price`](/packages/framework/src/Model/Pricing/Price.php) is also an immutable [value object](https://codete.com/blog/value-objects/) used in pricing.

It represents a price with and without VAT and is used in many parts of Shopsys Framework.
Price calculation classes usually output instances of `Price`.

It can be constructed by calling `new Price(Money $priceWithoutVat, Money $priceWithVat)`.
For a zero price, you can use a short-hand method `Price::zero()`.

The class has three getters you can use to retrieve the prices or the VAT amount:
- `Price::getPriceWithoutVat() : Money`
- `Price::getPriceWithVat() : Money`
- `Price::getVatAmount() : Money`

And you can calculate with prices using its methods:
- `Price::add(Price $addend) : Price`
- `Price::subtract(Price $subtrahend) : Price`
- `Price::inverse() : Price`
