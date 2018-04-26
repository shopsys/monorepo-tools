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
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductEditDataFactory
     */
    protected $productEditDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFacade
     */
    protected $imageFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductVariantService
     */
    protected $productVariantService;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
     */
    protected $productPriceRecalculationScheduler;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler
     */
    protected $productAvailabilityRecalculationScheduler;

    public function __construct(
        EntityManagerInterface $em,
        ProductFacade $productFacade,
        ProductEditDataFactory $productEditDataFactory,
        ImageFacade $imageFacade,
        ProductVariantService $productVariantService,
        ProductPriceRecalculationScheduler $productPriceRecalculationScheduler,
        ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler
    ) {
        $this->em = $em;
        $this->productFacade = $productFacade;
        $this->productEditDataFactory = $productEditDataFactory;
        $this->imageFacade = $imageFacade;
        $this->productVariantService = $productVariantService;
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
        $this->productVariantService->checkProductIsNotMainVariant($mainProduct);

        $mainVariantEditData = $this->productEditDataFactory->createFromProduct($mainProduct);
        $mainVariant = $this->productVariantService->createMainVariant($mainVariantEditData, $mainProduct, $variants);
        $this->em->persist($mainVariant);

        try {
            $toFlush = $mainVariant->getVariants();
            $toFlush[] = $mainVariant;
            $this->em->flush($toFlush);
            $this->productFacade->setAdditionalDataAfterCreate($mainVariant, $mainVariantEditData);
            $this->imageFacade->copyImages($mainProduct, $mainVariant);
        } catch (\Exception $exception) {
            $this->productAvailabilityRecalculationScheduler->cleanScheduleForImmediateRecalculation();
            $this->productPriceRecalculationScheduler->cleanScheduleForImmediateRecalculation();

            throw $exception;
        }

        return $mainVariant;
    }
}
