<?php

namespace Shopsys\ShopBundle\Model\Product\Pricing;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade;
use Shopsys\ShopBundle\Component\Domain\DomainFacade;
use Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductInputPriceService;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductManualInputPriceRepository;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductRepository;
use Shopsys\ShopBundle\Model\Product\ProductService;

class ProductInputPriceFacade {

    const BATCH_SIZE = 50;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade
     */
    private $entityManagerFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductInputPriceService
     */
    private $productInputPriceService;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\PricingSetting
     */
    private $pricingSetting;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductManualInputPriceRepository
     */
    private $productManualInputPriceRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\DomainFacade
     */
    private $domainFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupFacade
     */
    private $pricingGroupFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductService
     */
    private $productService;

    /**
     * @var \Doctrine\ORM\Internal\Hydration\IterableResult|\Shopsys\ShopBundle\Model\Product\Product[][0]|null
     */
    private $productRowsIterator;

    public function __construct(
        EntityManager $em,
        EntityManagerFacade $entityManagerFacade,
        ProductInputPriceService $productInputPriceService,
        CurrencyFacade $currencyFacade,
        PricingSetting $pricingSetting,
        ProductManualInputPriceRepository $productManualInputPriceRepository,
        PricingGroupFacade $pricingGroupFacade,
        DomainFacade $domainFacade,
        ProductRepository $productRepository,
        ProductService $productService
    ) {
        $this->em = $em;
        $this->entityManagerFacade = $entityManagerFacade;
        $this->productInputPriceService = $productInputPriceService;
        $this->currencyFacade = $currencyFacade;
        $this->pricingSetting = $pricingSetting;
        $this->productManualInputPriceRepository = $productManualInputPriceRepository;
        $this->domainFacade = $domainFacade;
        $this->pricingGroupFacade = $pricingGroupFacade;
        $this->productRepository = $productRepository;
        $this->productService = $productService;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return string|null
     */
    public function getInputPrice(Product $product) {
        $inputPriceType = $this->pricingSetting->getInputPriceType();
        $defaultCurrency = $this->currencyFacade->getDefaultCurrency();
        $manualInputPricesInDefaultCurrency = $this->productManualInputPriceRepository->getByProductAndDomainConfigs(
            $product,
            $this->domainFacade->getDomainConfigsByCurrency($defaultCurrency)
        );

        return $this->productInputPriceService->getInputPrice($product, $inputPriceType, $manualInputPricesInDefaultCurrency);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return string[pricingGroupId]
     */
    public function getManualInputPricesData(Product $product) {
        $pricingGroups = $this->pricingGroupFacade->getAll();
        $inputPriceType = $this->pricingSetting->getInputPriceType();
        $manualInputPrices = $this->productManualInputPriceRepository->getByProduct($product);

        return $this->productInputPriceService->getManualInputPricesData(
            $product,
            $inputPriceType,
            $pricingGroups,
            $manualInputPrices
        );
    }

    /**
     * @return bool
     */
    public function replaceBatchVatAndRecalculateInputPrices() {
        if ($this->productRowsIterator === null) {
            $this->productRowsIterator = $this->productRepository->getProductIteratorForReplaceVat();
        }

        for ($count = 0; $count < self::BATCH_SIZE; $count++) {
            $row = $this->productRowsIterator->next();
            if ($row === false) {
                $this->em->flush();
                $this->entityManagerFacade->clear();

                return false;
            }
            $product = $row[0];
            $newVat = $product->getVat()->getReplaceWith();
            $productManualInputPrices = $this->productManualInputPriceRepository->getByProduct($product);
            $this->productService->recalculateInputPriceForNewVatPercent($product, $productManualInputPrices, $newVat->getPercent());
            $product->changeVat($newVat);
        }

        $this->em->flush();
        $this->entityManagerFacade->clear();

        return true;
    }

}
