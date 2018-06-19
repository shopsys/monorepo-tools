<?php

namespace Shopsys\FrameworkBundle\Model\Product\Detail;

use Shopsys\FrameworkBundle\Component\Image\ImageFacade;
use Shopsys\FrameworkBundle\Model\Localization\Localization;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterRepository;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;

class ProductDetailFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser
     */
    private $productPriceCalculationForUser;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterRepository
     */
    private $parameterRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFacade
     */
    private $imageFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\Localization
     */
    private $localization;

    public function __construct(
        ProductPriceCalculationForUser $productPriceCalculationForUser,
        ProductRepository $productRepository,
        ParameterRepository $parameterRepository,
        ImageFacade $imageFacade,
        Localization $localization
    ) {
        $this->productPriceCalculationForUser = $productPriceCalculationForUser;
        $this->productRepository = $productRepository;
        $this->parameterRepository = $parameterRepository;
        $this->imageFacade = $imageFacade;
        $this->localization = $localization;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Product\Detail\ProductDetail
     */
    public function getDetailForProduct(Product $product)
    {
        return new ProductDetail($product, $this);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $products
     * @return \Shopsys\FrameworkBundle\Model\Product\Detail\ProductDetail[]
     */
    public function getDetailsForProducts(array $products)
    {
        $details = [];

        foreach ($products as $product) {
            $details[] = $this->getDetailForProduct($product);
        }

        return $details;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice|null
     */
    public function getSellingPrice(Product $product)
    {
        try {
            $productPrice = $this->productPriceCalculationForUser->calculatePriceForCurrentUser($product);
        } catch (\Shopsys\FrameworkBundle\Model\Product\Pricing\Exception\MainVariantPriceCalculationException $ex) {
            $productPrice = null;
        }
        return $productPrice;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValue[]
     */
    public function getParameters(Product $product)
    {
        $locale = $this->localization->getLocale();

        $productParameterValues = $this->parameterRepository->getProductParameterValuesByProductSortedByName($product, $locale);
        foreach ($productParameterValues as $index => $productParameterValue) {
            $parameter = $productParameterValue->getParameter();
            if ($parameter->getName() === null
                || $productParameterValue->getValue()->getLocale() !== $locale
            ) {
                unset($productParameterValues[$index]);
            }
        }

        return $productParameterValues;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Component\Image\Image[]
     */
    public function getImagesIndexedById(Product $product)
    {
        return $this->imageFacade->getImagesByEntityIndexedById($product, null);
    }
}
