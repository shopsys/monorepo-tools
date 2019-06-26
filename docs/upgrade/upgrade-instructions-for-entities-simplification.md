# Upgrade Instructions for Entities Simplification

In [#1123](https://github.com/shopsys/shopsys/pull/1123), the entities were simplified so they are now independent of other services.
This rule is written down and you can read more in [Introduction to Model Architecture](/docs/model/introduction-to-model-architecture.md) article.
The change introduced many [BC breaks](/docs/contributing/backward-compatibility-promise.md) (many methods were moved or changed their signatures) so you need to follow the upgrading instructions.

## Following methods have been removed:
- `Order::calculateTotalPrice()`
    - use `OrderPriceCalculation::getOrderTotalPrice()` to calculate the price and set it using `Order::setTotalPrice()` instead
- `Order::addProduct()`
    - use `OrderItemFactoryInterface::createProduct()` to create the order item and add it to the order using `Order::addItem()` instead
- `Order::fillOrderPayment()`
    - use `OrderFacade::fillOrderPayment()` instead
- `Order::fillOrderTransport()`
    - use `OrderFacade::fillOrderTransport()` instead
- `Order::fillOrderProducts()`
    - use `OrderFacade::fillOrderProducts()` instead
- `Order::addOrderItemDiscount()`
    - use `OrderFacade::addOrderItemDiscount()` instead
- `Order::fillOrderRounding()`
    - use `OrderFacade::fillOrderRounding()` instead
- `Order::calculateOrderItemDataPrices()`
    - use `OrderFacade::calculateOrderItemDataPrices()` instead
- `Cart::mergeWithCart()`
    - use `CartMigrationFacade::mergeCurrentCartWithCart` instead
- `Cart::addProduct()`
    - use `CartFacade::addProductToCart()` instead
- `Product::setCategories()`
    - use `Product::setProductCategoryDomains()` instead and pass array of `ProductCategoryDomain` instances as an parameter
- `Product::createNewProductCategoryDomains()`
- `Product::removeOldProductCategoryDomains()`
- `ProductManualInputPrice::recalculateInputPriceForNewVatPercent()`
    - use `ProductInputPriceRecalculator::recalculateInputPriceForNewVatPercent()` instead
- `FriendlyUrl::getAbsoluteUrl()`
    - use `FriendlyUrlFacade::getAbsoluteUrlByFriendlyUrl()` instead
- `User::changeEmail()`
    - use `CustomerFacade::setEmail()` instead
- `User::changePassword()`
    - use `CustomerPasswordFacade::changePassword()` instead
- `User::setNewPassword()`
    - use `CustomerPasswordFacade::setNewPassword()` instead
- `User::editDeliveryAddress()`
    - use `CustomerFacade::editDeliveryAddress()` instead
- `Administrator::checkForDelete()`
    - use `AdministratorFacade::checkForDelete()` instead
- `Administrator::setPassword()`
    - use `AdministratorFacade::setPassword()` instead
- `Administrator::rememberGridLimit()`
    - use `AdministratorGridFacade::rememberGridLimit()` instead

## Following methods have changed their signatures:
```diff
- Order::edit(OrderData $orderData, OrderItemPriceCalculation $orderItemPriceCalculation,OrderItemFactoryInterface $orderItemFactory, OrderPriceCalculation $orderPriceCalculation)
+ Order::edit(OrderData $orderData)
```
```diff
- Product::__construct(ProductData $productData, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory, ?array $variants = null)
+ Product::__construct(ProductData $productData, ?array $variants = null)
```
```diff
- Product::create(ProductData $productData, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory)
+ Product::create(ProductData $productData)
```
```diff
- Product::createMainVariant(ProductData $productData, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory, array $variants)
+ Product::createMainVariant(ProductData $productData, array $variants)
```
```diff
- Product::edit(ProductCategoryDomainFactoryInterface $productCategoryDomainFactory, array $productCategoryDomains, ProductData $productData, ProductPriceRecalculationScheduler $productPriceRecalculationScheduler)
+ Product::edit(array $productCategoryDomains, ProductData $productData)
```
```diff
- Product::addVariant(self $variant, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory)
+ Product::addVariant(self $variant)
```
```diff
- Product::addVariants(array $variants, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory)
+ Product::addVariants(array $variants)
```
```diff
- Product::refreshVariants(array $currentVariants, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory)
+ Product::refreshVariants(array $currentVariants)
```
```diff
- Product::addNewVariants(array $currentVariants, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory)
+ Product::addNewVariants(array $currentVariants)
```
```diff
- ProductFactory::__construct(EntityNameResolver $entityNameResolver, ProductAvailabilityCalculation $productAvailabilityCalculation, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory)
+ ProductFactory::__construct(EntityNameResolver $entityNameResolver, ProductAvailabilityCalculation $productAvailabilityCalculation)
```
```diff
- ProductInputPriceFacade::__construct(EntityManagerInterface $em, CurrencyFacade $currencyFacade, PricingSetting $pricingSetting, ProductManualInputPriceRepository $productManualInputPriceRepository, PricingGroupFacade $pricingGroupFacade, ProductRepository $productRepository, BasePriceCalculation $basePriceCalculation, InputPriceCalculation $inputPriceCalculation)
+ ProductInputPriceFacade::__construct(EntityManagerInterface $em, CurrencyFacade $currencyFacade, PricingSetting $pricingSetting, ProductManualInputPriceRepository $productManualInputPriceRepository, PricingGroupFacade $pricingGroupFacade, ProductRepository $productRepository, ProductInputPriceRecalculator $productInputPriceRecalculator)
```
```diff
- User::__construct(UserData $userData, BillingAddress $billingAddress, ?DeliveryAddress $deliveryAddress, ?self $userByEmail)
+ User::__construct(UserData $userData, BillingAddress $billingAddress, ?DeliveryAddress $deliveryAddress)
```
```diff
- UserFactory::create(UserData $userData, BillingAddress $billingAddress, ?DeliveryAddress $deliveryAddress, ?User $userByEmail)
+ UserFactory::create(UserData $userData, BillingAddress $billingAddress, ?DeliveryAddress $deliveryAddress)
```
```diff
- CustomerFacade::__construct(EntityManagerInterface $em, UserRepository $userRepository, CustomerDataFactoryInterface $customerDataFactory, EncoderFactoryInterface $encoderFactory, CustomerMailFacade $customerMailFacade, BillingAddressFactoryInterface $billingAddressFactory, DeliveryAddressFactoryInterface $deliveryAddressFactory, BillingAddressDataFactoryInterface $billingAddressDataFactory, UserFactoryInterface $userFactory)
+ CustomerFacade::__construct(EntityManagerInterface $em, UserRepository $userRepository, CustomerDataFactoryInterface $customerDataFactory, CustomerMailFacade $customerMailFacade, BillingAddressFactoryInterface $billingAddressFactory, DeliveryAddressFactoryInterface $deliveryAddressFactory, BillingAddressDataFactoryInterface $billingAddressDataFactory, UserFactoryInterface $userFactory, CustomerPasswordFacade $customerPasswordFacade)
```
```diff
- User::edit(UserData $userData, EncoderFactoryInterface $encoderFactory)
+ User::edit(UserData $userData)
```
```diff
- User::__construct(EntityNameResolver $entityNameResolver, EncoderFactoryInterface $encoderFactory)
+ User::__construct(EntityNameResolver $entityNameResolver, CustomerPasswordFacade $customerPasswordFacade)
```
```diff
- User::resetPassword(HashGenerator $hashGenerator)
+ User::resetPassword(string $resetPasswordHash)
```
```diff
- Administrator::edit(AdministratorData $administratorData, EncoderFactoryInterface $encoderFactory, ?self $administratorByUserName)
+ Administrator::edit(AdministratorData $administratorData)
```

## Additional information:
- protected constant `DEFAULT_PRODUCT_QUANTITY` has been moved from `Order` to `OrderItemFacade`
- `InvalidQuantifiedProductException` has been removed
- there is a new method `ProductCategoryDomainFactoryInterface::createMultiple()` that needs to be implemented
    - you can get inspired by the [implementation in the framework package](https://github.com/shopsys/framework/blob/v8.0.0/src/Model/Product/ProductCategoryDomainFactory.php#L44)
- `Product::edit()` no longer schedules product for price recalculation
    - you need to use `ProductFacade::edit()`
- `CustomerPasswordFacade::setNewPassword()` is now strictly type hinted
- protected constant `RESET_PASSWORD_HASH_LENGTH` has been moved from `User` to `CustomerPasswordFacade` and made public
- use `AdministratorFacade::edit` instead of `Administrator::edit()` if you want to keep the checking of the admin's username uniqueness

## Tests:
- in many tests, methods that changed their signatures are called
    - running `php phing phpstan` should help you to discover the problems
- remove `Tests\ShopBundle\Functional\Model\Cart\CartTest::testMergeWithCartReturnsCartWithSummedProducts()`  
    - add new `Tests\ShopBundle\Functional\Model\Cart\CartMigrationFacadeTest` class, you can copy-paste it from [Github](https://github.com/shopsys/project-base/blob/v8.0.0/tests/ShopBundle/Functional/Model/Cart/CartMigrationFacadeTest.php)
- move tests from `Tests\ShopBundle\Functional\Model\Cart\CartTest` to `Tests\ShopBundle\Functional\Model\Cart\CartFacadeTest`
and fix them appropriately (you can copy paste them from [Github](https://github.com/shopsys/project-base/blob/v8.0.0/tests/ShopBundle/Functional/Model/Cart/CartFacadeTest.php))
    - `testCannotAddProductFloatQuantityToCart()`
    - `testCannotAddProductZeroQuantityToCart()`
    - `testCannotAddProductNegativeQuantityToCart()`
    - `testAddProductToCartMarksAddedProductAsNew()`
    - `testAddProductToCartMarksRepeatedlyAddedProductAsNotNew()`
    - `testAddProductResultContainsAddedProductQuantity()`
    - `testAddProductResultDoesNotContainPreviouslyAddedProductQuantity()`
- fix `Tests\ShopBundle\Functional\Model\Order\OrderFacadeTest::testCreate()`:
```diff
- /** @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculation */
- $productPriceCalculation = $this->getContainer()->get(ProductPriceCalculationForUser::class);
- /** @var \Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactory $cartItemFactory */
- $cartItemFactory = $this->getContainer()->get(CartItemFactory::class);
- $cart->addProduct($product, 1, $productPriceCalculation, $cartItemFactory);
+ $cartFacade->addProductToCart($product->getId(), 1);
```
- move `Tests\FrameworkBundle\Unit\Model\Product\ProductTest::testEditSchedulesPriceRecalculation` to `ProductFacadeTest`
    - you can copy paste it from [Github](https://github.com/shopsys/project-base/blob/v8.0.0/tests/ShopBundle/Functional/Model/Product/ProductFacadeTest.php#L133)
- rename `Tests\ShopBundle\Functional\Model\Pricing\ProductManualInputPriceTest` to `Tests\ShopBundle\Functional\Model\Pricing\ProductInputPriceRecalculatorTest` and use instance of `ProductInputPriceRecalculator` for input prices recalculations
    - you can copy paste the class from [Github](https://github.com/shopsys/project-base/blob/v8.0.0/tests/ShopBundle/Functional/Model/Pricing/ProductInputPriceRecalculatorTest.php)
- rename `Tests\ShopBundle\Functional\Model\Order\OrderEditTest` to `Tests\ShopBundle\Functional\Model\Order\OrderFacadeEditTest`
    - change the test to is uses `OrderFacade` to edit the orders instead of calling `Order::edit()` directly
    - you'll have to modify the *act* phase of the test:
        ```diff
        - $this->order->edit($orderData, $this->orderItemPriceCalculation, $this->orderItemFactory, $this->orderPriceCalculation);
        + $this->orderFacade->edit(self::ORDER_ID, $orderData);
        ```
    - and modify the `setUp()` method to set up the dependencies correctly
        - add `OrderFacade` from the DIC
        - remove `OrderItemPriceCalculation`, `OrderItemFactoryInterface` and `OrderPriceCalculation`
    - you can see the test class on [Github](https://github.com/shopsys/project-base/blob/v8.0.0/tests/ShopBundle/Functional/Model/Order/OrderFacadeEditTest.php)
- remove the following tests from `Tests\FrameworkBundle\Unit\Model\Customer\UserTest` and create new `Tests\ShopBundle\Functional\Model\Customer\CustomerFacadeTest` class that will test the behavior
(you can copy paste the class from [Github](https://github.com/shopsys/project-base/blob/v8.0.0/tests/ShopBundle/Functional/Model/Customer/CustomerFacadeTest.php))
    - `testChangeEmailToExistingEmailButDifferentDomainDoNotThrowException()`
    - `testCreateNotDuplicateEmail()`
    - `testCreateDuplicateEmail()`
    - `testCreateDuplicateEmailCaseInsentitive()`
