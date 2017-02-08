<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Component\Controller\FrontBaseController;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Product\TopProduct\TopProductFacade;
use SS6\ShopBundle\Model\Seo\SeoSettingFacade;
use SS6\ShopBundle\Model\Slider\SliderItemFacade;

class HomepageController extends FrontBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CurrentCustomer
	 */
	private $currentCustomer;

	/**
	 * @var \SS6\ShopBundle\Model\Product\TopProduct\TopProductFacade
	 */
	private $topProductFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Seo\SeoSettingFacade
	 */
	private $seoSettingFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Slider\SliderItemFacade
	 */
	private $sliderItemFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
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

		return $this->render('@SS6Shop/Front/Content/Default/index.html.twig', [
			'sliderItems' => $sliderItems,
			'topProductsDetails' => $topProductsDetails,
			'title' => $this->seoSettingFacade->getTitleMainPage($this->domain->getId()),
			'metaDescription' => $this->seoSettingFacade->getDescriptionMainPage($this->domain->getId()),
		]);
	}

}
