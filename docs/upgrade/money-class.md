# Upgrade Instructions for Money Class

This article describes upgrade instructions for [#821 new class for representing monetary values](https://github.com/shopsys/shopsys/pull/821).
Upgrade instructions are in a separate article because there is a lot of instructions and we don't want to jam the [UPGRADE-v7.0.0.md](/docs/upgrade/UPGRADE-v7.0.0.md).
Follow these instructions only if you upgrade from `v7.0.0-beta6` to `v7.0.0`.

All monetary values (*prices, account balances, discount amounts, price limits etc.*) should be represented by an instance of the class `\Shopsys\FrameworkBundle\Component\Money\Money`.

Before the upgrade, we recommend reading the article [How to Work with Money](/docs/model/how-to-work-with-money.md) which explains the concept in detail.

**When in doubt, you can take a look at [what was modified in `shopsys/project-base` during this change](https://github.com/shopsys/project-base/compare/cb6d02f335819aeff575dec01bda5b228263a2eb...c08cac7b55ebc46b43c2e988d36e2f122cbb4598#files_bucket).**

## Configuration
Register the new Doctrine type in `doctrine.dbal.types` in your `app/config/packages/doctrine.yml`:
```diff
         password: "%database_password%"
         charset: UTF8
         server_version: "%database_server_version%"
+        types:
+            money: \Shopsys\FrameworkBundle\Component\Doctrine\MoneyType

     orm:
         auto_generate_proxy_classes: false
```

## Extended and Custom Entities
If you have created your custom entities that store monetary value or added a new such column [by entity extension](/docs/extensibility/entity-extension.md), change your `decimal` Doctrine type to the new `money` type:

```diff
  /**
-  * @var string
-  * @ORM\Column(type="decimal", precision=20, scale=6)
+  * @var \Shopsys\FrameworkBundle\Component\Money\Money
+  * @ORM\Column(type="money", precision=20, scale=6)
   */
  protected $myMonetaryValue;
```

If you do so, you'll have to create [a database migration](/docs/introduction/database-migrations.md) which will add a `(DC2Type:money)` comment on the columns, informing Doctrine about the specified type (similarly to the [`Version20190215092226`](/packages/framework/src/Migrations/Version20190215092226.php) in `shopsys/framework`).

Because the entity property now changed the type, its usages (along with the related [entity data class](/docs/model/entities.md#entity-data)) have to be reviewed and changed as well.
We recommend using type-hinting in the changed methods as explained in [Working With Money in PHP](#working-with-money-in-php) below.

If you need to set a value to the property in these custom entities or data objects directly, you should use the `Money::create()` method that accepts a string or an integer (or another [construction method](/docs/model/how-to-work-with-money.md#construction)).

For more details read [the Money in Doctrine section](/docs/model/how-to-work-with-money.md#money-in-doctrine) of the documentation.

## Working With Money in PHP
You should use `Money` for all monetary values across your project.
We strongly recommend declaring the `Money` type for both [the method arguments](http://php.net/manual/en/functions.arguments.php#functions.arguments.type-declaration) and [the return values](http://php.net/manual/en/functions.returning-values.php#functions.returning-values.type-declaration) in your methods.

If you need to compute money, use its methods `add`, `subtract`, `multiply` and `divide` instead of the operators `+`, `-`, `*` and `/`.

If you need to compare two instances of `Money`, use its methods `equals`, `isGreaterThan`, `isGreaterThanOrEqualTo`, `isLessThan`, `isLessThanOrEqualTo` and `compare` instead of the operators `==`/`===`, `>`, `>=`, `<`, `<=` and `<=>`.
If you want to compare `Money` with zero, you can use the methods `isZero`, `isPositive` and `isNegative`.

For more details read [the Money Class section](/docs/model/how-to-work-with-money.md#money-class) of the documentation.

There were no changes needed in `shopsys/project-base` as most PHP implementation is in `shopsys/framework`.
You might need to update your custom classes working with prices and overridden services, because of the changed types.

*Note: Running the `phpstan` [Phing target](/docs/introduction/console-commands-for-application-management-phing-targets.md) will help you find out problems with incompatible method declarations mentioned in this section and in [Strict Typing](#strict-typing) below.*
*In case of problems, it will fail on a `PHP Fatal error` with a reference to the incompatible method (unfortunately it is unable to continue with the checks as it cannot recover from this error).*

## Strict Typing
In the process of spreading the use of `Money` across the methods, we enabled [strict typing](http://php.net/manual/en/functions.arguments.php#functions.arguments.type-declaration.strict) in some classes.
A type declaration was added into all method parameters and return values when possible, so you can be sure about the accepted and expected variable types.
Strict typing was also enabled in all newly created classes.

Check how you use or extend these classes, into which the statement `declare(strict_types=1);` was added:
- `\Shopsys\FrameworkBundle\Model\Cart\Item\CartItem`
- `\Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation`
- `\Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedItemPrice`
- `\Shopsys\FrameworkBundle\Model\Order\OrderPriceCalculation`
- `\Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreviewCalculation`
- `\Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCode`
- `\Shopsys\FrameworkBundle\Model\Order\Watcher\TransportAndPaymentWatcher`
- `\Shopsys\FrameworkBundle\Model\Payment\PaymentPrice`
- `\Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation`
- `\Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation`
- `\Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation`
- `\Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation`
- `\Shopsys\FrameworkBundle\Model\Pricing\Rounding`
- `\Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculation`
- `\Shopsys\FrameworkBundle\Model\Product\Pricing\QuantifiedProductDiscountCalculation`
- `\Shopsys\FrameworkBundle\Model\Product\Pricing\QuantifiedProductPriceCalculation`
- `\Shopsys\FrameworkBundle\Model\Transport\TransportPrice`
- `\Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation`
- `\Shopsys\FrameworkBundle\Twig\PriceExtension`

*Note: Running the `phpstan` [Phing target](/docs/introduction/console-commands-for-application-management-phing-targets.md) will help you find out problems with incompatible method declarations as mentioned in the note above.*

## Twig Templates
Templates mostly use [the `price` Twig filter](/docs/model/how-to-work-with-money.md#price) (or one of its `price*` variants) for rendering monetary values.
The filters work the same way, but they now require an instance of `Money` to be provided.
If you pass a value of a different type into one of the `price*` filters (it would fail on a `TypeError`) you should check where it came from and fix it there - all monetary values have to be represented by the new `Money` value object.
Because the monetary value is mostly taken from a `Price` object and its getters use `Money` as a return type now, the templates usually don't need any changes.

*Note: [HTTP smoke tests](/docs/introduction/automated-testing.md#http-smoke-tests) can be very helpful when finding issues in your templates.*
*You can run them by executing the `tests-smoke` [Phing target](/docs/introduction/console-commands-for-application-management-phing-targets.md).*

There are some cases in which you'll have to modify your templates:

### Manipulation with money before rendering
Instead of arithmetic operators, you should use the methods for [computation with Money](/docs/model/how-to-work-with-money.md#computation) such as `add`, `subtract`, `multiply`, etc.
Also, you can use the `Price::inverse()` for inverting the value, which might come handy in presenting discounts.

As an example, see the change in `@ShopsysShop/Front/Content/Order/preview.html.twig` from `shopsys/project-base`:
```diff
-    {{ (-quantifiedProductDiscount.priceWithVat)|price }}
+    {{ quantifiedProductDiscount.inverse.priceWithVat|price }}
```

Code manipulating with prices in a complex way does not belong into templates.
In such situation, you should probably refactor your code and move the logic into your [model](/docs/model/introduction-to-model-architecture.md).

### Comparing monetary values in templates
Instead of comparison operators, you should use the methods for [comparing Money](/docs/model/how-to-work-with-money.md#comparing) such as `equals`, `isGreaterThan`, `isGreaterOrEqualTo`, etc.
To compare a value with zero, you may use `isZero`, `isPositive`, or `isNegative`.

Don't forget to check conditions without any operators (such as `{% if money %}` / `{% if not money %}`).
These could have been used to compare with zero, but now the actual value would have no effect on the conditions as [every object is converted to `true`](http://php.net/manual/en/language.types.boolean.php#language.types.boolean.casting) in PHP.
You should replace such conditions with the negated `isZero` call (such as `{% if not money.isZero %}` / `{% if money.isZero %}`).

For values of type `Money|null` you'll have to check the `null` value separately to avoid calling methods on `null`, eg.: `{% if money is not null and not money.isZero %}`.

As an example, see the change in `@ShopsysShop/Front/Content/PersonalData/orders.xml.twig` from `shopsys/project-base`:
```diff
-    {% if item.priceWithoutVat %}
-        <item_unit_price_without_vat>{{ item.priceWithoutVat }}</item_unit_price_without_vat>
+    {% if not item.priceWithoutVat.isZero %}
+        <item_unit_price_without_vat>{{ item.priceWithoutVat|moneyFormat }}</item_unit_price_without_vat>
     {% endif %}
-    {% if item.priceWithVat %}
-        <item_unit_price_with_vat>{{ item.priceWithVat }}</item_unit_price_with_vat>
+    {% if not item.priceWithVat.isZero %}
+        <item_unit_price_with_vat>{{ item.priceWithVat|moneyFormat }}</item_unit_price_with_vat>
```

### Displaying money without a currency symbol
Instead of rendering a monetary value directly, you should use [the new `moneyFormat` Twig filter](/docs/model/how-to-work-with-money.md#moneyformat) which accepts `Money` instances.
So instead of `{{ money }}`, you should use `{{ money|moneyFormat }}`.

See all the changes that were needed in `shopsys/project-base`:
- in `@ShopsysShop/Front/Content/Product/detail.html.twig`:
    ```diff
        content="{{ currencyCode(domain.id) }}"
        >
        <meta itemprop="price"
    -        content="{{ getProductSellingPrice(product).priceWithVat }}"
    +        content="{{ getProductSellingPrice(product).priceWithVat|moneyFormat }}"
        >
        <link itemprop="availability"
            href="{{ product.calculatedSellingDenied ? 'http://schema.org/OutOfStock' : 'http://schema.org/InStock' }}"
    ```
    ```diff
        content="{{ currencyCode(domain.id) }}"
        >
        <meta itemprop="lowPrice"
    -        content="{{ getProductSellingPrice(product).priceWithVat }}"
    +        content="{{ getProductSellingPrice(product).priceWithVat|moneyFormat }}"
        >
        <link itemprop="availability"
            href="{{ product.calculatedSellingDenied ? 'http://schema.org/OutOfStock' : 'http://schema.org/InStock' }}"
    ```
- in `@ShopsysShop/Front/Content/Product/filterFormMacro.html.twig`:
    ```diff
        <div
                class="js-range-slider"
                data-minimum-input-id="{{ filterForm.minimalPrice.vars.id }}"
    -            data-minimal-value="{{ priceRange.minimalPrice }}"
    +            data-minimal-value="{{ priceRange.minimalPrice|moneyFormat }}"
                data-maximum-input-id="{{ filterForm.maximalPrice.vars.id }}"
    -            data-maximal-value="{{ priceRange.maximalPrice }}"
    +            data-maximal-value="{{ priceRange.maximalPrice|moneyFormat }}"
            ></div>
        </div>
    ```
- in `@ShopsysShop/Front/Inline/MeasuringScript/googleAnalyticsEcommerce.html.twig`:
    ```diff
        ga('ecommerce:addTransaction', {
            'id': '{{ order.number|escape('js') }}',
    -       'revenue': '{{ order.totalPriceWithVat }}',
    -       'shipping': '{{ order.transportAndPaymentPrice.priceWithVat }}',
    -       'tax': '{{ order.totalVatAmount }}',
    +       'revenue': '{{ order.totalPriceWithVat|moneyFormat }}',
    +       'shipping': '{{ order.transportAndPaymentPrice.priceWithVat|moneyFormat }}',
    +       'tax': '{{ order.totalVatAmount|moneyFormat }}',
            'currency': '{{ order.currency.code|escape('js') }}'
        });
    ```
    ```diff
                    'category': '{{ productMainCategory.name|escape('js') }}',
                    {% endif %}
                {% endif %}
    -           'price': '{{ orderProduct.priceWithVat }}',
    +           'price': '{{ orderProduct.priceWithVat|moneyFormat }}',
                'quantity': '{{ orderProduct.quantity }}'
            });
        {% endfor %}
    ```

## Forms
Review all usage of the `MoneyType` field in your project.

If you are using `MoneyType` field for non-monetary values, change it to `NumberType` instead.
Otherwise, take in mind that its value will automatically convert to a `Money` instance now.

This means you cannot use constraints that are validating scalar values (`GreaterThan`, `Range`, etc.).
You can keep using `NotBlank` to validate `null` values and use the new constraints `NotNegativeMoneyAmount` and `MoneyRange`:
- `Shopsys\FrameworkBundle\Form\Constraints\MoneyRange` for setting acceptable range (only one side of the interval is required)
- `Shopsys\FrameworkBundle\Form\Constraints\NotNegativeMoneyAmount` for setting that entered amount of money has to be zero or higher

If none of these constraints is sufficient for your use case, create your own.
For inspiration, [see the commit where we implemented the MoneyRange constraint](https://github.com/shopsys/shopsys/pull/821/commits/390b21416e54ca8bf34a7f7deec4e5c3eed5cd51).

*Note: You don't have to specify the option `'currency' => false` for the `MoneyType` field as this is its new default value.*

You'll find more info about the form type and constraints in [the Money in Forms section](/docs/model/how-to-work-with-money.md#money-in-forms) of the documentation.

---

As a rather complex example, you can see how we changed the `ProductFilterFormType` class in `shopsys/project-base` (which was the only form changed in the repository).

Note how the constraints have been changed from `GreaterThanOrEqual` to `NotNegativeMoneyAmount`.

The `'currency' => false` and  `'scale' => 2` options have been removed as those are the default values for the options.

The most significant was the newly added protected method `transformMoneyToView` that uses the same model and view transformers as the `MoneyType` to transform the placeholders from `Money` to a string.
Previously, the `MoneyToLocalizedStringTransformer` (which is a default view transformer in the `MoneyType` field) was used explicitly - now, the transformers are taken from the passed `FormBuilder`.

```diff
  namespace Shopsys\ShopBundle\Form\Front\Product;

+ use Shopsys\FrameworkBundle\Component\Money\Money;
+ use Shopsys\FrameworkBundle\Form\Constraints\NotNegativeMoneyAmount;
  // ...
- use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;
  // ...
- use Symfony\Component\Validator\Constraints;

  class ProductFilterFormType extends AbstractType
  {
      // ...
      public function buildForm(FormBuilderInterface $builder, array $options)
      {
-         $priceScale = 2;
-         $priceTransformer = new MoneyToLocalizedStringTransformer($priceScale, false);
          /** @var \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfig $config */
          $config = $options['product_filter_config'];

+         $moneyBuilder = $builder->create('money', MoneyType::class);
+
          $builder
              ->add('minimalPrice', MoneyType::class, [
-                 'currency' => false,
-                 'scale' => $priceScale,
                  'required' => false,
-                 'attr' => ['placeholder' => $priceTransformer->transform($config->getPriceRange()->getMinimalPrice())],
+                 'attr' => ['placeholder' => $this->transformMoneyToView($config->getPriceRange()->getMinimalPrice(), $moneyBuilder)],
                  'invalid_message' => 'Please enter price in correct format (positive number with decimal separator)',
                  'constraints' => [
-                     new Constraints\GreaterThanOrEqual([
-                         'value' => 0,
-                         'message' => 'Price must be greater or equal to {{ compared_value }}',
-                     ]),
+                     new NotNegativeMoneyAmount(['message' => 'Price must be greater or equal to zero']),
                  ],
              ])
              ->add('maximalPrice', MoneyType::class, [
-                 'currency' => false,
-                 'scale' => $priceScale,
                  'required' => false,
-                 'attr' => ['placeholder' => $priceTransformer->transform($config->getPriceRange()->getMaximalPrice())],
+                 'attr' => ['placeholder' => $this->transformMoneyToView($config->getPriceRange()->getMaximalPrice(), $moneyBuilder)],
                  'invalid_message' => 'Please enter price in correct format (positive number with decimal separator)',
                  'constraints' => [
-                     new Constraints\GreaterThanOrEqual([
-                         'value' => 0,
-                         'message' => 'Price must be greater or equal to {{ compared_value }}',
-                     ]),
+                     new NotNegativeMoneyAmount(['message' => 'Price must be greater or equal to zero']),
                  ],
              ])
      // ...
+
+     /**
+      * @param \Shopsys\FrameworkBundle\Component\Money\Money $money
+      * @param \Symfony\Component\Form\FormBuilderInterface $moneyBuilder
+      * @return string
+      */
+     protected function transformMoneyToView(Money $money, FormBuilderInterface $moneyBuilder): string
+     {
+         foreach ($moneyBuilder->getModelTransformers() as $modelTransformer) {
+             /** @var \Symfony\Component\Form\DataTransformerInterface $modelTransformer */
+             $money = $modelTransformer->transform($money);
+         }
+         foreach ($moneyBuilder->getViewTransformers() as $viewTransformer) {
+             /** @var \Symfony\Component\Form\DataTransformerInterface $viewTransformer */
+             $money = $viewTransformer->transform($money);
+         }
+
+         return $money;
+     }
  }
```

## Data in SettingValue Entities
The [`Setting`](/packages/framework/src/Component/Setting/Setting.php) component is now able to save and load [`SettingValue`](/packages/framework/src/Component/Setting/SettingValue.php) entities with an instance of `Money` as a value.
To make sure that monetary values that are currently saved as a decimal string will be loaded as a `Money` instance, you have to manually create [a database migration](/docs/introduction/database-migrations.md) that changes the `type` to `money` for all `SettingValue` entities holding monetary values that you added into your project.
Previously, any of types `string`, `integer` or `float` might have been used to save a numeric value into the database.

For example see the `up` method of migration class [`Version20190220101938`](/packages/framework/src/Migrations/Version20190220101938.php) that migrates the `freeTransportAndPaymentPriceLimit` setting values to the `Money` type:
```php
/**
 * @param \Doctrine\DBAL\Schema\Schema $schema
 */
public function up(Schema $schema)
{
    $this->sql('UPDATE setting_values SET type = \'money\' WHERE name = \'freeTransportAndPaymentPriceLimit\' AND type != \'none\'');
}
```

*Note: If you didn't add any new `SettingValue` entities that hold monetary values you can safely skip this step.*
*The migration in `shopsys/framework` mentioned above will take care of changing the type of the limits for free transport and payment in your DB.*

## Data Fixtures
All monetary values in data fixtures should use `Money` as well.
If you haven't altered the data fixtures in any way, you should be ready to go when you copy the data fixtures from `shopsys/project-base` in `v7.0.0` (as explained in the upgrade notes of [PR #854](https://github.com/shopsys/shopsys/pull/854/files#diff-da18024d2b6b8b4b2fafe94d18cb2866)).

You should manage to upgrade your custom data fixtures by using [the static methods `Money::create()` and `Money::zero()`](/docs/model/how-to-work-with-money.md#construction) when setting a monetary value.

These 3 data fixture classes had to be changed in `shopsys/project-base`:
- `\Shopsys\ShopBundle\DataFixtures\Demo\PaymentDataFixture.php`
- `\Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader.php`
- `\Shopsys\ShopBundle\DataFixtures\Demo\TransportDataFixture.php`

*Note: Thanks to [the parameter type declarations](http://php.net/manual/en/functions.arguments.php#functions.arguments.type-declaration) you should be able to see very quickly if your data fixtures use a wrong type when you run the `db-demo` [Phing target](/docs/introduction/console-commands-for-application-management-phing-targets.md).*

## Tests
Changes will be required in all your [unit tests](/docs/introduction/automated-testing.md#unit-tests) and [functional tests](/docs/introduction/automated-testing.md#functional-tests) that deal with money.

You'll have to create instances of `Money` instead of using integers, floats or strings in your data providers (or directly in your test methods) like in these examples:

```diff
- $cartItem = $cartItemFactory->create($cart, $product, 1, 10);
+ $cartItem = $cartItemFactory->create($cart, $product, 1, Money::create(10));
```

```diff
  public function inputPricesTestDataProvider()
  {
      return [
          [
-             'inputPriceWithoutVat' => '100',
-             'inputPriceWithVat' => '121',
+             'inputPriceWithoutVat' => Money::create(100),
+             'inputPriceWithVat' => Money::create(121),
              'vatPercent' => '21',
          ],
          [
-             'inputPriceWithoutVat' => '17261.983471',
-             'inputPriceWithVat' => '20887',
+             'inputPriceWithoutVat' => Money::create('17261.983471'),
+             'inputPriceWithVat' => Money::create(20887),
              'vatPercent' => '21',
          ],
      ];
  }
```

Also, when you assert that two monetary values are equal, you should use the `$this->assertThat` method with the new constraint `IsMoneyEqual` instead of asserting the equality (you can drop the rounding as well).
Take a look at the examples:

```diff
+ use Tests\FrameworkBundle\Test\IsMoneyEqual;
  // ...
- $this->assertSame('1000', (string)$productManualInputPrice->getInputPrice());
+ $this->assertThat($productManualInputPrice->getInputPrice(), new IsMoneyEqual(Money::create(1000)));
```

```diff
+ use Tests\FrameworkBundle\Test\IsMoneyEqual;
  // ...
- $this->assertSame(round($inputPriceWithoutVat, 6), round($payment->getPrice($currency)->getPrice(), 6));
- $this->assertSame(round($inputPriceWithoutVat, 6), round($transport->getPrice($currency)->getPrice(), 6));
+ $this->assertThat($payment->getPrice($currency)->getPrice(), new IsMoneyEqual($inputPriceWithoutVat));
+ $this->assertThat($transport->getPrice($currency)->getPrice(), new IsMoneyEqual($inputPriceWithoutVat));
```

```diff
+ use Tests\FrameworkBundle\Test\IsMoneyEqual;
  // ...
  $this->assertNull($orderPreview->getPayment());
  $this->assertNull($orderPreview->getPaymentPrice());
- $this->assertSame(400 * 2, $orderPreview->getTotalPrice()->getVatAmount());
- $this->assertSame(2400 * 2, $orderPreview->getTotalPrice()->getPriceWithVat());
- $this->assertSame(2000 * 2, $orderPreview->getTotalPrice()->getPriceWithoutVat());
+ $this->assertThat($orderPreview->getTotalPrice()->getVatAmount(), new IsMoneyEqual(Money::create(400 * 2)));
+ $this->assertThat($orderPreview->getTotalPrice()->getPriceWithVat(), new IsMoneyEqual(Money::create(2400 * 2)));
+ $this->assertThat($orderPreview->getTotalPrice()->getPriceWithoutVat(), new IsMoneyEqual(Money::create(2000 * 2)));
  $this->assertNull($orderPreview->getTransport());
  $this->assertNull($orderPreview->getTransportPrice());
```

Furthermore, you'll have to apply same changes as in [section Working with Money in PHP](#working-with-money-in-php) if your tests make arithmetic operations or comparisons with Money.

You can read more about it in [the Unit and Functional Tests section](/docs/model/how-to-work-with-money.md#unit-and-functional-tests).

These 13 functional test classes had to be changed in `shopsys/project-base`:
- `\Tests\ShopBundle\Functional\Model\Cart\CartFacadeDeleteOldCartsTest`
- `\Tests\ShopBundle\Functional\Model\Cart\CartFacadeTest`
- `\Tests\ShopBundle\Functional\Model\Cart\CartItemTest`
- `\Tests\ShopBundle\Functional\Model\Cart\CartTest`
- `\Tests\ShopBundle\Functional\Model\Cart\Watcher.php`
- `\Tests\ShopBundle\Functional\Model\Order\OrderFacadeTest`
- `\Tests\ShopBundle\Functional\Model\Order\Preview\OrderPreviewCalculationTest`
- `\Tests\ShopBundle\Functional\Model\Pricing\InputPriceRecalculationSchedulerTest`
- `\Tests\ShopBundle\Functional\Model\Pricing\ProductManualInputPriceTest`
- `\Tests\ShopBundle\Functional\Model\Product\ProductOnCurrentDomainFacadeTest`
- `\Tests\ShopBundle\Functional\Model\Product\ProductVisibilityRepositoryTest`
- `\Tests\ShopBundle\Functional\PersonalData\PersonalDataExportXmlTest`
- `\Tests\ShopBundle\Functional\Twig\PriceExtensionTest`

Your [HTTP smoke tests](/docs/introduction/automated-testing.md#http-smoke-tests) and [acceptance test](/docs/introduction/automated-testing.md#acceptance-tests-aka-functional-tests-or-selenium-tests) shouldn't require any changes.

## Javascript
The `Money` class implements the `JsonSerializable` interface so it can be serialized using `json_encode` into an object usable in JS.

You can read more about it in [the Money in Javascript section](/docs/model/how-to-work-with-money.md#money-in-javascript) of the documentation if you work with monetary values using JS in your project.
There were no changes needed in `shopsys/project-base`.

## Other
The method `Cart::getQuantifiedProductsIndexedByItemId()` was renamed to `Cart::getQuantifiedProducts()` and no longer uses the product IDs for indexing.
This was done due to the fact that it could lead to unexpected behavior when the `CartItem` entities have not yet been persisted (and thus they don't have been assigned an ID).

Similarly, `CartFacade::getQuantifiedProductsOfCurrentCustomerIndexedByCartItemId()` was renamed to `CartFacade::getQuantifiedProductsOfCurrentCustomer()` and also no longer uses the product IDs for indexing.

Check your usage of the methods and make appropriate changes.

Note that the indexing may be used in Twig templates as well.
As an example, see the change that was required in `@ShopsysShop/Front/Content/Cart/index.html.twig` in `shopsys/project-base`:
```diff

    <tbody>
-       {% for cartItem in cartItems %}
-           {% set cartItemPrice = cartItemPrices[cartItem.id] %}
-           {% set cartItemDiscount = cartItemDiscounts[cartItem.id] %}
+       {% for index, cartItem in cartItems %}
+           {% set cartItemPrice = cartItemPrices[index] %}
+           {% set cartItemDiscount = cartItemDiscounts[index] %}
                <tr class="table-cart__row js-cart-item">
                    <td class="table-cart__cell table-cart__cell--image">
```

If you extended `QuantifiedProductPriceCalculation`, check your usage of the protected methods `getTotalPriceWithoutVat()`, `getTotalPriceWithVat()` and `getTotalPriceVatAmount()`.
The signatures were changed and they now require two parameters to be provided.

This was done to prevent the protected parameters `$quantifiedProduct`, `$product`, and `$productPrice` from being used to pass values between methods.
These protected parameters were removed.
Refactor your code if you have used them (you can take a look at [the original commit for inspiration](https://github.com/shopsys/shopsys/pull/821/commits/deb643e3d9d3ee077488250d50cc10936086c6a5)).

---

We know that the change is huge and upgrading might be difficult.
We want to keep improving Shopsys Framework for you, we hope that this article helped you to safely upgrade your project.
If you find anything we missed or if you'll need to explain anything more, please [create an issue](https://github.com/shopsys/shopsys/issues/new) or contact us on [our Slack](http://slack.shopsys-framework.com/).
