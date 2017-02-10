<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\ShopBundle\Component\Controller\FrontBaseController;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Customer\CurrentCustomer;
use Shopsys\ShopBundle\Model\Product\TopProduct\TopProductFacade;
use Shopsys\ShopBundle\Model\Seo\SeoSettingFacade;
use Shopsys\ShopBundle\Model\Slider\SliderItemFacade;

class HomepageController extends FrontBaseController
{

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\TopProduct\TopProductFacade
     */
    private $topProductFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Seo\SeoSettingFacade
     */
    private $seoSettingFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Slider\SliderItemFacade
     */
    private $sliderItemFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(
        CurrentCustomer $currentCustomer,
        SliderItemFacade $sliderItemFacade,
        TopProductFacade $topProductsFacade,
        SeoSettingFacade $seoSettingFacade,
        Domain $domain
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->sliderItemFacade = $sliderItemFacade;
        $this->topProductFacade = $topProductsFacade;
        $this->seoSettingFacade = $seoSettingFacade;
        $this->domain = $domain;
    }

    public function indexAction() {
        $sliderItems = $this->sliderItemFacade->getAllVisibleOnCurrentDomain();
        $topProductsDetails = $this->topProductFacade->getAllOfferedProductDetails(
            $this->domain->getId(),
            $this->currentCustomer->getPricingGroup()
        );

        return $this->render('@ShopsysShop/Front/Content/Default/index.html.twig', [
            'sliderItems' => $sliderItems,
            'topProductsDetails' => $topProductsDetails,
            'title' => $this->seoSettingFacade->getTitleMainPage($this->domain->getId()),
            'metaDescription' => $this->seoSettingFacade->getDescriptionMainPage($this->domain->getId()),
        ]);
    }

}
