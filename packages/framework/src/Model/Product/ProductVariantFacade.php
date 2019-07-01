<?php

namespace Shopsys\FrameworkBundle\Model\Product;

use BadMethodCallException;
use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;
use Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExportScheduler;

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
     * @var \Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExportScheduler
     */
    protected $productSearchExportScheduler;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface $productDataFactory
     * @param \Shopsys\FrameworkBundle\Component\Image\ImageFacade $imageFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductFactoryInterface $productFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler $productPriceRecalculationScheduler
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExportScheduler|null $productSearchExportScheduler
     */
    public function __construct(
        EntityManagerInterface $em,
        ProductFacade $productFacade,
        ProductDataFactoryInterface $productDataFactory,
        ImageFacade $imageFacade,
        ProductFactoryInterface $productFactory,
        ProductPriceRecalculationScheduler $productPriceRecalculationScheduler,
        ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler,
        ?ProductSearchExportScheduler $productSearchExportScheduler = null
    ) {
        $this->em = $em;
        $this->productFacade = $productFacade;
        $this->productDataFactory = $productDataFactory;
        $this->imageFacade = $imageFacade;
        $this->productFactory = $productFactory;
        $this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
        $this->productAvailabilityRecalculationScheduler = $productAvailabilityRecalculationScheduler;
        $this->productSearchExportScheduler = $productSearchExportScheduler;
    }

    /**
     * @required
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExportScheduler $productSearchExportScheduler
     * @deprecated Will be replaced with constructor injection in the next major release
     */
    public function setProductSearchExportScheduler(ProductSearchExportScheduler $productSearchExportScheduler): void
    {
        if ($this->productSearchExportScheduler !== null && $this->productSearchExportScheduler !== $productSearchExportScheduler) {
            throw new BadMethodCallException(sprintf('Method "%s" has been already called and cannot be called multiple times.', __METHOD__));
        }

        if ($this->productSearchExportScheduler === null) {
            @trigger_error(sprintf('The %s() method is deprecated and will be removed in the next major. Use the constructor injection instead.', __METHOD__), E_USER_DEPRECATED);

            $this->productSearchExportScheduler = $productSearchExportScheduler;
        }
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

        $this->productSearchExportScheduler->scheduleProductIdForImmediateExport($mainVariant->getId());
        foreach ($mainVariant->getVariants() as $variant) {
            $this->productSearchExportScheduler->scheduleProductIdForImmediateExport($variant->getId());
        }

        return $mainVariant;
    }
}
