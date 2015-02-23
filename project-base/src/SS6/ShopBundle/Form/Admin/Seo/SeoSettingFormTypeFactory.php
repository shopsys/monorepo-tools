<?php

namespace SS6\ShopBundle\Form\Admin\Seo;

use SS6\ShopBundle\Form\Admin\Seo\SeoSettingFormType;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Seo\SeoSettingFacade;

class SeoSettingFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\Seo\SeoSettingFacade
	 */
	private $seoSettingFacade;

	public function __construct(
		Domain $domain,
		SelectedDomain $selectedDomain,
		SeoSettingFacade $seoSettingFacade
	) {
		$this->domain = $domain;
		$this->selectedDomain = $selectedDomain;
		$this->seoSettingFacade = $seoSettingFacade;
	}

	/**
	 * @return SS6\ShopBundle\Form\Admin\Seo\SeoSettingFormType
	 */
	public function create() {
		return new SeoSettingFormType(
			$this->getTitlesOnOtherDomains($this->domain, $this->selectedDomain, $this->seoSettingFacade),
			$this->getTitleAddOnsOnOtherDomains($this->domain, $this->selectedDomain, $this->seoSettingFacade),
			$this->getDescriptionsOnOtherDomains($this->domain, $this->selectedDomain, $this->seoSettingFacade)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Domain\Domain $domain
	 * @param \SS6\ShopBundle\Model\Domain\SelectedDomain $selectedDomain
	 * @param \SS6\ShopBundle\Model\Seo\SeoSettingFacade $seoSettingFacade
	 * @return string[]
	 */
	private function getTitlesOnOtherDomains(
		Domain $domain,
		SelectedDomain $selectedDomain,
		SeoSettingFacade $seoSettingFacade
	) {
		$titles = [];
		foreach ($domain->getAll() as $domainConfig) {
			$title = $seoSettingFacade->getTitleMainPage($domainConfig->getId());
			if ($title !== null && $domainConfig->getId() !== $selectedDomain->getId()) {
				$titles[] = $title;
			}
		}

		return $titles;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Domain\Domain $domain
	 * @param \SS6\ShopBundle\Model\Domain\SelectedDomain $selectedDomain
	 * @param \SS6\ShopBundle\Model\Seo\SeoSettingFacade $seoSettingFacade
	 * @return string[]
	 */
	private function getTitleAddOnsOnOtherDomains(
		Domain $domain,
		SelectedDomain $selectedDomain,
		SeoSettingFacade $seoSettingFacade
	) {
		$titlesAddOns = [];
		foreach ($domain->getAll() as $domainConfig) {
			$titlesAddOn = $seoSettingFacade->getTitleAddOn($domainConfig->getId());
			if ($titlesAddOn !== null && $domainConfig->getId() !== $selectedDomain->getId()) {
				$titlesAddOns[] = $titlesAddOn;
			}
		}

		return $titlesAddOns;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Domain\Domain $domain
	 * @param \SS6\ShopBundle\Model\Domain\SelectedDomain $selectedDomain
	 * @param \SS6\ShopBundle\Model\Seo\SeoSettingFacade $seoSettingFacade
	 * @return string[]
	 */
	private function getDescriptionsOnOtherDomains(
		Domain $domain,
		SelectedDomain $selectedDomain,
		SeoSettingFacade $seoSettingFacade
	) {
		$descriptions = [];
		foreach ($domain->getAll() as $domainConfig) {
			$description = $seoSettingFacade->getDescriptionMainPage($domainConfig->getId());
			if ($description !== null && $domainConfig->getId() !== $selectedDomain->getId()) {
				$descriptions[] = $description;
			}
		}

		return $descriptions;
	}
}
