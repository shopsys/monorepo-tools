<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;
use Shopsys\FrameworkBundle\Model\Product\ProductService;

class ProductInputPriceFacade
{
    const BATCH_SIZE = 50;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductInputPriceService
     */
    protected $productInputPriceService;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade
     */
    protected $currencyFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting
     */
    protected $pricingSetting;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPriceRepository
     */
    protected $productManualInputPriceRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\DomainFacade
     */
    protected $domainFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade
     */
    protected $pricingGroupFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductService
     */
    protected $productService;

    /**
     * @var \Doctrine\ORM\Internal\Hydration\IterableResult|\Shopsys\FrameworkBundle\Model\Product\Product[][]|null
     */
    protected $productRowsIterator;

    public function __construct(
        EntityManagerInterface $em,
        ProductInputPriceService $productInputPriceService,
        CurrencyFacade $currencyFacade,
        PricingSetting $pricingSetting,
        ProductManualInputPriceRepository $productManualInputPriceRepository,
        PricingGroupFacade $pricingGroupFacade,
        ProductRepository $productRepository,
        ProductService $productService
    ) {
        $this->em = $em;
        $this->productInputPriceService = $productInputPriceService;
        $this->currencyFacade = $currencyFacade;
        $this->pricingSetting = $pricingSetting;
        $this->productManualInputPriceRepository = $productManualInputPriceRepository;
        $this->pricingGroupFacade = $pricingGroupFacade;
        $this->productRepository = $productRepository;
        $this->productService = $productService;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return string|null
     */
    public function getInputPrice(Product $product)
    {
        $inputPriceType = $this->pricingSetting->getInputPriceType();
        $defaultCurrency = $this->currencyFacade->getDefaultCurrency();
        $manualInputPricesInDefaultCurrency = $this->productManualInputPriceRepository->getByProductAndDomainConfigs(
            $product,
            $this->currencyFacade->getDomainConfigsByCurrency($defaultCurrency)
        );

        return $this->productInputPriceService->getInputPrice($product, $inputPriceType, $manualInputPricesInDefaultCurrency);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return string[]
     */
    public function getManualInputPricesDataIndexedByPricingGroupId(Product $product)
    {
        $pricingGroups = $this->pricingGroupFacade->getAll();
        $inputPriceType = $this->pricingSetting->getInputPriceType();
        $manualInputPrices = $this->productManualInputPriceRepository->getByProduct($product);

        return $this->productInputPriceService->getManualInputPricesDataIndexedByPricingGroupId(
            $product,
            $inputPriceType,
            $pricingGroups,
            $manualInputPrices
        );
    }

    /**
     * @return bool
     */
    public function replaceBatchVatAndRecalculateInputPrices()
    {
        if ($this->productRowsIterator === null) {
            $this->productRowsIterator = $this->productRepository->getProductIteratorForReplaceVat();
        }

        for ($count = 0; $count < self::BATCH_SIZE; $count++) {
            $row = $this->productRowsIterator->next();
            if ($row === false) {
                $this->em->flush();
                $this->em->clear();

                return false;
            }
            $product = $row[0];
            $newVat = $product->getVat()->getReplaceWith();
            $productManualInputPrices = $this->productManualInputPriceRepository->getByProduct($product);
            $this->productService->recalculateInputPriceForNewVatPercent($product, $productManualInputPrices, $newVat->getPercent());
            $product->changeVat($newVat);
        }

        $this->em->flush();
        $this->em->clear();

        return true;
    }
}
