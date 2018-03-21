<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Doctrine\EntityManagerFacade;
use Shopsys\FrameworkBundle\Component\Domain\DomainFacade;
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
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Doctrine\EntityManagerFacade
     */
    private $entityManagerFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductInputPriceService
     */
    private $productInputPriceService;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting
     */
    private $pricingSetting;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPriceRepository
     */
    private $productManualInputPriceRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\DomainFacade
     */
    private $domainFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade
     */
    private $pricingGroupFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductService
     */
    private $productService;

    /**
     * @var \Doctrine\ORM\Internal\Hydration\IterableResult|\Shopsys\FrameworkBundle\Model\Product\Product[][]|null
     */
    private $productRowsIterator;

    public function __construct(
        EntityManagerInterface $em,
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
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return string|null
     */
    public function getInputPrice(Product $product)
    {
        $inputPriceType = $this->pricingSetting->getInputPriceType();
        $defaultCurrency = $this->currencyFacade->getDefaultCurrency();
        $manualInputPricesInDefaultCurrency = $this->productManualInputPriceRepository->getByProductAndDomainConfigs(
            $product,
            $this->domainFacade->getDomainConfigsByCurrency($defaultCurrency)
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
