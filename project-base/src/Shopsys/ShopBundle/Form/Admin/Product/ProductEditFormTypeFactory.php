<?php

namespace Shopsys\ShopBundle\Form\Admin\Product;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Image\ImageFacade;
use Shopsys\ShopBundle\Component\Transformers\ImagesIdsToImagesTransformer;
use Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer;
use Shopsys\ShopBundle\Form\Admin\Product\Parameter\ProductParameterValueFormTypeFactory;
use Shopsys\ShopBundle\Form\Admin\Product\ProductFormTypeFactory;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Seo\SeoSettingFacade;

class ProductEditFormTypeFactory {

    /**
     * @var \Shopsys\ShopBundle\Form\Admin\Product\Parameter\ProductParameterValueFormTypeFactory
     */
    private $productParameterValueFormTypeFactory;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\ImageFacade
     */
    private $imageFacade;

    /**
     * @var \Shopsys\ShopBundle\Form\Admin\Product\ProductFormTypeFactory
     */
    private $productFormTypeFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupFacade
     */
    private $pricingGroupFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Model\Seo\SeoSettingFacade
     */
    private $seoSettingFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer
     */
    private $removeDuplicatesFromArrayTransformer;

    /**
     * @var \Shopsys\ShopBundle\Component\Transformers\ImagesIdsToImagesTransformer
     */
    private $imagesIdsToImagesTransformer;

    public function __construct(
        ProductParameterValueFormTypeFactory $productParameterValueFormTypeFactory,
        ImageFacade $imageFacade,
        ProductFormTypeFactory $productFormTypeFactory,
        PricingGroupFacade $pricingGroupFacade,
        Domain $domain,
        SeoSettingFacade $seoSettingFacade,
        RemoveDuplicatesFromArrayTransformer $removeDuplicatesFromArrayTransformer,
        ImagesIdsToImagesTransformer $imagesIdsToImagesTransformer
    ) {
        $this->productParameterValueFormTypeFactory = $productParameterValueFormTypeFactory;
        $this->imageFacade = $imageFacade;
        $this->productFormTypeFactory = $productFormTypeFactory;
        $this->pricingGroupFacade = $pricingGroupFacade;
        $this->domain = $domain;
        $this->seoSettingFacade = $seoSettingFacade;
        $this->removeDuplicatesFromArrayTransformer = $removeDuplicatesFromArrayTransformer;
        $this->imagesIdsToImagesTransformer = $imagesIdsToImagesTransformer;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product|null $product
     * @return \Shopsys\ShopBundle\Form\Admin\Product\ProductFormType
     */
    public function create(Product $product = null) {
        if ($product !== null) {
            $images = $this->imageFacade->getImagesByEntityIndexedById($product, null);
        } else {
            $images = [];
        }

        $pricingGroups = $this->pricingGroupFacade->getAll();
        $domains = $this->domain->getAll();
        $metaDescriptionsIndexedByDomainId = $this->seoSettingFacade->getDescriptionsMainPageIndexedByDomainIds($domains);

        return new ProductEditFormType(
            $images,
            $this->productParameterValueFormTypeFactory,
            $this->productFormTypeFactory,
            $pricingGroups,
            $domains,
            $metaDescriptionsIndexedByDomainId,
            $this->removeDuplicatesFromArrayTransformer,
            $this->imagesIdsToImagesTransformer,
            $product
        );
    }

}
