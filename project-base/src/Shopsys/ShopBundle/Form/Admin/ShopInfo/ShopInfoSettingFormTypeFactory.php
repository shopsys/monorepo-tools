<?php

namespace SS6\ShopBundle\Form\Admin\ShopInfo;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Domain\SelectedDomain;
use SS6\ShopBundle\Model\ShopInfo\ShopInfoSettingFacade;

class ShopInfoSettingFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\ShopInfo\ShopInfoSettingFacade
	 */
	private $shopInfoSettingFacade;

	public function __construct(
		Domain $domain,
		SelectedDomain $selectedDomain,
		ShopInfoSettingFacade $shopInfoSettingFacade
	) {
		$this->domain = $domain;
		$this->selectedDomain = $selectedDomain;
		$this->shopInfoSettingFacade = $shopInfoSettingFacade;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\ShopInfo\ShopInfoSettingFormType
	 */
	public function create() {
		return new ShopInfoSettingFormType();
	}
}
