# Upgrade Instructions for Services Removal

This article describes upgrade instructions for [#627 model service layer removal](https://github.com/shopsys/shopsys/pull/627).
Upgrade instructions are in a separate article because there is a lot of instructions and we don't want to jam UPGRADE.md.
Follow these instructions only if you upgrade from `v7.0.0-beta4` to `v7.0.0-beta5`.

We removed services (i.e. *Service classes - not to be interchanged with Symfony services) from our model and moved logic into more suitable places.
Following instructions tell you which method was moved where, so you can upgrade your code and also tests.

If you use these methods, change their calling appropriately:
- `CustomerService::edit(User $user, UserData $userData)`  
  -> `User::edit(UserData $userData, CustomerPasswordService $customerPasswordService)`
- `CustomerService::createDeliveryAddress(DeliveryAddressData $deliveryAddressData)`  
  -> `DeliveryAddressFactory::create(DeliveryAddressData $data)`
- `CustomerService::editDeliveryAddress(User $user, DeliveryAddressData $deliveryAddressData, DeliveryAddress $deliveryAddress = null)`  
  -> `User::editDeliveryAddress(DeliveryAddressData $deliveryAddressData, DeliveryAddressFactoryInterface $deliveryAddressFactory)`
- `CustomerService::changeEmail(User $user, $email, User $userByEmail = null)`  
  -> `User::changeEmail(string $email, ?self $userByEmail)`
- `CustomerService::create(UserData $userData, BillingAddress $billingAddress, DeliveryAddress $deliveryAddress = null, User $userByEmail = null)`  
  -> `UserFactory::create(UserData $userData, BillingAddress $billingAddress, ?DeliveryAddress $deliveryAddress, ?User $userByEmail)`
- `CustomerService::getAmendedByOrder(User $user, Order $order)`  
  -> `CustomerDataFactoryInterface::createAmendedCustomerDataByOrder(User $user, Order $order)`
- `AdministratorGridService::rememberGridLimit(Administrator $administrator, Grid $grid)`  
  -> `Administrator::rememberGridLimit(Grid $grid, AdministratorGridLimitFactoryInterface $administratorGridLimitFactory)`
- `AdministratorGridService::restoreGridLimit(Administrator $administrator, Grid $grid)`  
  -> `Administrator::restoreGridLimit(Grid $grid)`
- `AdministratorService::setPassword(Administrator $administrator, $password)`  
  -> `Administrator::setPassword(string $password, EncoderFactoryInterface $encoderFactory)`
- `AdministratorService::edit(AdministratorData $administratorData, Administrator $administrator, Administrator $administratorByUserName = null)`  
  -> `Administrator::edit(AdministratorData $administratorData, EncoderFactoryInterface $encoderFactory, ?self $administratorByUserName)`
- `AdministratorService::delete(Administrator $administrator, $adminCountExcludingSuperadmin)`  
  -> `Administrator::checkForDelete(TokenStorageInterface $tokenStorage, int $adminCountExcludingSuperadmin)`
- `OrderService::calculateTotalPrice(Order $order)`  
  -> `Order::calculateTotalPrice(OrderPriceCalculation $orderPriceCalculation)`
- `ProductService::getProductSellingPricesIndexedByDomainIdAndPricingGroupId(Product $product, array $pricingGroups)`  
  -> `ProductFacade::getAllProductSellingPricesIndexedByDomainId(Product $product)`
- `ProductService::sortProductsByProductIds(array $products, array $orderedProductIds)`  
  -> `ProductRepository::getSortedProductsByIds($domainId, PricingGroup $pricingGroup, array $sortedProductIds)`
- `ProductService::markProductForVisibilityRecalculation(Product $product)`  
  -> `Product::markForVisibilityRecalculation()`
- `ProductService::edit(Product $product, ProductData $productData)`  
  -> `Product::edit(ProductCategoryDomainFactoryInterface $productCategoryDomainFactory, ProductData $productData, ProductPriceRecalculationScheduler $productPriceRecalculationScheduler)`
- `ProductService::delete(Product $product)`  
  -> `Product::getProductDeleteResult()`
- `ProductService::recalculateInputPriceForNewVatPercent(Product $product, $productManualInputPrices, $newVatPercent)`  
  -> `ProductManualInputPrice::recalculateInputPriceForNewVatPercent($inputPriceType, $newVatPercent, BasePriceCalculation $basePriceCalculation, InputPriceCalculation $inputPriceCalculation)`
- `OrderService::getOrderDetailUrl(Order $order)`  
  -> `OrderUrlGenerator::getOrderDetailUrl(Order $order)`
- `OrderService:: createOrderProductInOrder(Order $order, Product $product, Price $productPrice)`  
  -> `Order::addProduct(Product $product, Price $productPrice, OrderProductFactoryInterface $orderProductFactory, Domain $domain, OrderPriceCalculation $orderPriceCalculation)`
- `OrderService::editOrder(Order $order, OrderData $orderData)`  
  -> `Order::edit(OrderData $orderData, OrderItemPriceCalculation $orderItemPriceCalculation, OrderProductFactoryInterface $orderProductFactory, OrderPriceCalculation $orderPriceCalculation)`
- `OrderStatusService::checkForDelete`  
  -> `OrderStatus::checkForDelete`
- `OrderStatusService::createOrderProductInOrder`  
  -> `OrderStatus::addProductToOrder`
- `RegistrationMailService::getMessageDataByUser(User $user, MailTemplate $mailTemplate)`  
  -> `RegistrationMail::createMessage(MailTemplate $mailTemplate, $user)`
- `OrderMailService::getMessageDataByOrder(Order $order, MailTemplate $mailTemplate)`  
  -> `OrderMail::createMessage(MailTemplate $mailTemplate, $order)`
- `ProductManualInputPriceService::refresh(Product $product, PricingGroup $pricingGroup, $inputPrice, $productManualInputPrice)`  
  -> `ProductManualInputPriceFacade::refresh(Product $product, PricingGroup $pricingGroup, $inputPrice)`
- `ProductCollectionService::getImagesIndexedByProductId(array $products, array $imagesByProductId)`  
  -> `ProductCollectionFacade::getMainImagesIndexedByProductId(array $products)`
- `CategoryService::create(CategoryData $categoryData, Category $rootCategory)`
  -> `CategoryFactory::create(CategoryData $data, Category $rootCategory)`
- `CategoryService::edit(Category $category, CategoryData $categoryData, Category $rootCategory)`
  -> `CategoryFacade::edit($categoryId, CategoryData $categoryData)`
- `CurrencyService::edit(Currency $currency, CurrencyData $currencyData, $isDefaultCurrency)`
  -> `CurrencyFacade::edit($currencyId, CurrencyData $currencyData)`
- `CurrencyService::getNotAllowedToDeleteCurrencyIds($defaultCurrencyId, array $currenciesUsedInOrders, PricingSetting $pricingSetting, Domain $domain)`
  -> `CurrencyFacade::getNotAllowedToDeleteCurrencyIds()`
- `PricingService::getMinimumPriceByPriceWithoutVat(array $prices)`
  -> `ProductPriceCalculation::getMinimumPriceByPriceWithoutVat(array $prices)`
- `PricingService::arePricesDifferent(array $prices)`
  -> `ProductPriceCalculation::arePricesDifferent(array $prices)`
- `GridOrderingService::setPosition($entity, $position)`
  -> `GridOrderingFacade::saveOrdering($entityClass, array $rowIds)`
- `UploadedFileFactoryInterface::create(string $entityName, int $entityId, ?string $temporaryFilename)`
  -> `UploadedFileFactoryInterface::create(string $entityName, int $entityId, array $temporaryFilenames)`
- `UploadedFileService::createUploadedFile(UploadedFileEntityConfig $uploadedFileEntityConfig, $entityId, array $temporaryFilenames)`
  -> `UploadedFileFactoryInterface::create(string $entityName, int $entityId, array $temporaryFilenames)`
- `AdvancedSearchOrderService::createDefaultRuleFormViewData($filterName)` and `AdvancedSearchService::createDefaultRuleFormViewData($filterName)`
  -> `RuleFormViewDataFactory::createDefault(string $filterName)`
- `AdvancedSearchOrderService::getRulesFormViewDataByRequestData(array $requestData = null)` and `AdvancedSearchService::getRulesFormViewDataByRequestData(array $requestData = null)`
  -> `RuleFormViewDataFactory::createFromRequestData(string $defaultFilterName, array $requestData = null)`
- `AdvancedSearchOrderService::extendQueryBuilderByAdvancedSearchData(QueryBuilder $queryBuilder, array $advancedSearchData)` and `AdvancedSearchService::extendQueryBuilderByAdvancedSearchData(QueryBuilder $queryBuilder, array $advancedSearchData)`
  -> `AdvancedSearchQueryBuilderExtender::extendByAdvancedSearchData(QueryBuilder $queryBuilder, array $advancedSearchData)`
- `OrderProductService::subtractOrderProductsFromStock(array $orderProducts)`
  -> `OrderProductFacade::subtractOrderProductsFromStock(array $orderProducts)`

Following classes have been removed:
- `AdministratorService`
- `AdministratorGridService`
- `CustomerService`
- `ProductService`
- `OrderService`
- `OrderStatusService`
- `FlagService`
- `ProductManualInputPriceService`
- `ProductCollectionService`
- `CategoryService`
- `CurrencyService`
- `PricingService`
- `VatService`
- `GridOrderingService`
- `UploadedFileService`
- `AdvancedSearchService`
- `AdvancedSearchOrderService`

Following methods have been removed:
- `User::setDeliveryAddress()`, use `User::editDeliveryAddress()` instead
- `Administrator::addGridLimit()`, use `Administrator::rememberGridLimit()` instead
- `Administrator::removeGridLimit()` as it was not used anywhere
- `Administrator::getLimitByGridId()` as it was not used anywhere
- `FlagService::create()`, use `FlagFactory::create()` instead
- `FlagService::edit()`, use `Flag::edit()` instead
- `CategoryService::setChildrenAsSiblings()`
- `CurrencyService::create()`, use `CurrencyFactory::create()` instead
- `VatService::getNewDefaultVat()`

Following classes changed constructors:
- `AdministratorFacade`
- `AdministratorGridFacade`
- `CachedBestsellingProductFacade`
- `CustomerFacade`
- `FlagFacade`
- `OrderItemFacade`
- `OrderProductFacade`
- `OrderMailService`
- `OrderEditResult`
- `OrderFacade`
- `ProductInputPriceFacade`
- `ProductPriceRecalculator`
- `ProductFacade`
- `UserFactory`
- `ProductManualInputPriceFacade`
- `ProductCollectionFacade`
- `CategoryFacade`
- `CurrencyFacade`
- `ProductPriceCalculation`
- `VatFacade`
- `GridOrderingFacade`
- `UploadedFileFacade`
- `UploadedFileFactory`
- `AdvancedSearchFacade`
- `AdvancedSearchOrderFacade`

Following functions visibility was changed to `protected` as there is no need to use them from outside of objects:
- `Administrator::getGridLimit()`
- `Order::setTotalPrice()`

Follow also additional upgrade instructions:
- Change return type of `DeliveryAddressFactory::create()` to `?DeliveryAddress` as it now returns `null` when `addressFilled` is `false`
- Change usage of `ProductListOrderingModeService::` constants to `ProductListOrderingConfig::` as they were moved to the Config class

Following classes were renamed, so change their usage appropriately:
- `JavascriptCompilerService` -> `JavascriptCompiler`
    - and also it's method `generateCompiledFiles()` -> `compile()`
- `SitemapService` -> `SitemapFilePrefixer`
- `BestsellingProductService` -> `BestsellingProductCombinator`
- `TransportAndPaymentWatcherService` -> `TransportAndPaymentWatcher`
- `CartWatcherService` -> `CartWatcher`
- `MailerService` -> `Mailer`
- `StatisticsService` -> `ValueByDateTimeDataPointFormatter`
- `HeurekaShopCertificationService` -> `HeurekaShopCertificationLocaleHelper`
- `LoginService` -> `Authenticator`
- `QueryBuilderService` -> `QueryBuilderExtender`
- `DomainUrlService` -> `DomainUrlReplacer`
- `DomainService` -> `DomainIconResizer`
- `TransactionalMasterRequestService` -> `TransactionalMasterRequestListener`
- `ImageGeneratorService` -> `ImageGenerator`
- `ImageProcessingService` -> `ImageProcessor`
- `InlineEditService` -> `InlineEditFacade`
- `CronService` -> `CronFilter`
- `ErrorService` -> `ErrorExtractor`
- `RegistrationMailService` -> `RegistrationMail`
- `OrderMailService` -> `OrderMail`
- `ProductListOrderingModeService` -> `RequestToOrderingModeIdConverter`
