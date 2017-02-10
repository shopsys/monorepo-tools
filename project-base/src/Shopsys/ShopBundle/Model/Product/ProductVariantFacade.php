<?php

namespace Shopsys\ShopBundle\Model\Product;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Image\ImageFacade;
use Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductEditDataFactory;
use Shopsys\ShopBundle\Model\Product\ProductFacade;
use Shopsys\ShopBundle\Model\Product\ProductService;
use Shopsys\ShopBundle\Model\Product\ProductVariantService;

class ProductVariantFacade
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductFacade
     */
    private $productFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductEditDataFactory
     */
    private $productEditDataFactory;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\ImageFacade
     */
    private $imageFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductVariantService
     */
    private $productVariantService;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
     */
    private $productPriceRecalculationScheduler;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler
     */
    private $productAvailabilityRecalculationScheduler;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductService
     */
    private $productService;

    public function __construct(
        EntityManager $em,
        ProductFacade $productFacade,
        ProductEditDataFactory $productEditDataFactory,
        ImageFacade $imageFacade,
        ProductVariantService $productVariantService,
        ProductPriceRecalculationScheduler $productPriceRecalculationScheduler,
        ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler,
        ProductService $productService
    ) {
        $this->em = $em;
        $this->productFacade = $productFacade;
        $this->productEditDataFactory = $productEditDataFactory;
        $this->imageFacade = $imageFacade;
        $this->productVariantService = $productVariantService;
        $this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
        $this->productAvailabilityRecalculationScheduler = $productAvailabilityRecalculationScheduler;
        $this->productService = $productService;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $mainProduct
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $variants
     * @return \Shopsys\ShopBundle\Model\Product\Product
     */
    public function createVariant(Product $mainProduct, array $variants) {
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
