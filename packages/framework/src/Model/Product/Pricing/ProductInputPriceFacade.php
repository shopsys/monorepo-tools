<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade;
use Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;

class ProductInputPriceFacade
{
    const BATCH_SIZE = 50;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

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
     * @var \Doctrine\ORM\Internal\Hydration\IterableResult|\Shopsys\FrameworkBundle\Model\Product\Product[][]|null
     */
    protected $productRowsIterator;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation
     */
    protected $basePriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation
     */
    protected $inputPriceCalculation;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade $currencyFacade
     * @param \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting $pricingSetting
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPriceRepository $productManualInputPriceRepository
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade $pricingGroupFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductRepository $productRepository
     * @param \Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation $basePriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation $inputPriceCalculation
     */
    public function __construct(
        EntityManagerInterface $em,
        CurrencyFacade $currencyFacade,
        PricingSetting $pricingSetting,
        ProductManualInputPriceRepository $productManualInputPriceRepository,
        PricingGroupFacade $pricingGroupFacade,
        ProductRepository $productRepository,
        BasePriceCalculation $basePriceCalculation,
        InputPriceCalculation $inputPriceCalculation
    ) {
        $this->em = $em;
        $this->currencyFacade = $currencyFacade;
        $this->pricingSetting = $pricingSetting;
        $this->productManualInputPriceRepository = $productManualInputPriceRepository;
        $this->pricingGroupFacade = $pricingGroupFacade;
        $this->productRepository = $productRepository;
        $this->basePriceCalculation = $basePriceCalculation;
        $this->inputPriceCalculation = $inputPriceCalculation;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Component\Money\Money[]|null[]
     */
    public function getManualInputPricesDataIndexedByPricingGroupId(Product $product)
    {
        $manualInputPricesDataByPricingGroupId = [];

        $manualInputPrices = $this->productManualInputPriceRepository->getByProduct($product);

        foreach ($manualInputPrices as $manualInputPrice) {
            $pricingGroupId = $manualInputPrice->getPricingGroup()->getId();
            $manualInputPricesDataByPricingGroupId[$pricingGroupId] =
                $manualInputPrice->getInputPrice() !== null ? Money::fromValue($manualInputPrice->getInputPrice()) : null;
        }

        return $manualInputPricesDataByPricingGroupId;
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
            $inputPriceType = $this->pricingSetting->getInputPriceType();
            foreach ($productManualInputPrices as $productManualInputPrice) {
                $productManualInputPrice->recalculateInputPriceForNewVatPercent(
                    $inputPriceType,
                    $newVat->getPercent(),
                    $this->basePriceCalculation,
                    $this->inputPriceCalculation
                );
            }
            $product->changeVat($newVat);
        }

        $this->em->flush();
        $this->em->clear();

        return true;
    }
}
