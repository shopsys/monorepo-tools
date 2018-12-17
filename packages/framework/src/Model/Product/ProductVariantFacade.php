<?php

namespace Shopsys\FrameworkBundle\Model\Product;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;
use Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;

class ProductVariantFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade
     */
    protected $productFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface
     */
    protected $productDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFacade
     */
    protected $imageFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductFactoryInterface
     */
    protected $productFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
     */
    protected $productPriceRecalculationScheduler;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler
     */
    protected $productAvailabilityRecalculationScheduler;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface $productDataFactory
     * @param \Shopsys\FrameworkBundle\Component\Image\ImageFacade $imageFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductFactoryInterface $productFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler $productPriceRecalculationScheduler
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler
     */
    public function __construct(
        EntityManagerInterface $em,
        ProductFacade $productFacade,
        ProductDataFactoryInterface $productDataFactory,
        ImageFacade $imageFacade,
        ProductFactoryInterface $productFactory,
        ProductPriceRecalculationScheduler $productPriceRecalculationScheduler,
        ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler
    ) {
        $this->em = $em;
        $this->productFacade = $productFacade;
        $this->productDataFactory = $productDataFactory;
        $this->imageFacade = $imageFacade;
        $this->productFactory = $productFactory;
        $this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
        $this->productAvailabilityRecalculationScheduler = $productAvailabilityRecalculationScheduler;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $mainProduct
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $variants
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function createVariant(Product $mainProduct, array $variants)
    {
        $mainProduct->checkIsNotMainVariant();

        $mainVariantData = $this->productDataFactory->createFromProduct($mainProduct);
        $mainVariant = $this->productFactory->createMainVariant($mainVariantData, $mainProduct, $variants);
        $this->em->persist($mainVariant);

        try {
            $toFlush = $mainVariant->getVariants();
            $toFlush[] = $mainVariant;
            $this->em->flush($toFlush);
            $this->productFacade->setAdditionalDataAfterCreate($mainVariant, $mainVariantData);
            $this->imageFacade->copyImages($mainProduct, $mainVariant);
        } catch (\Exception $exception) {
            $this->productAvailabilityRecalculationScheduler->cleanScheduleForImmediateRecalculation();
            $this->productPriceRecalculationScheduler->cleanScheduleForImmediateRecalculation();

            throw $exception;
        }

        return $mainVariant;
    }
}
