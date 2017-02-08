<?php

namespace SS6\ShopBundle\Form\Admin\Seo;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Domain\SelectedDomain;
use SS6\ShopBundle\Form\Admin\Seo\SeoSettingFormType;
use SS6\ShopBundle\Model\Seo\SeoSettingFacade;

class SeoSettingFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\SelectedDomain
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
	 * @return \SS6\ShopBundle\Form\Admin\Seo\SeoSettingFormType
	 */
	public function create() {
		return new SeoSettingFormType(
			$this->getTitlesOnOtherDomains(),
			$this->getTitleAddOnsOnOtherDomains(),
			$this->getDescriptionsOnOtherDomains()
		);
	}

	/**
	 * @return string[]
	 */
	private function getTitlesOnOtherDomains() {
		$titles = [];
		foreach ($this->domain->getAll() as $domainConfig) {
			$title = $this->seoSettingFacade->getTitleMainPage($domainConfig->getId());
			if ($title !== null && $domainConfig->getId() !== $this->selectedDomain->getId()) {
				$titles[] = $title;
			}
		}

		return $titles;
	}

	/**
	 * @return string[]
	 */
	private function getTitleAddOnsOnOtherDomains() {
		$titlesAddOns = [];
		foreach ($this->domain->getAll() as $domainConfig) {
			$titlesAddOn = $this->seoSettingFacade->getTitleAddOn($domainConfig->getId());
			if ($titlesAddOn !== null && $domainConfig->getId() !== $this->selectedDomain->getId()) {
				$titlesAddOns[] = $titlesAddOn;
			}
		}

		return $titlesAddOns;
	}

	/**
	 * @return string[]
	 */
	private function getDescriptionsOnOtherDomains() {
		$descriptions = [];
		foreach ($this->domain->getAll() as $domainConfig) {
			$description = $this->seoSettingFacade->getDescriptionMainPage($domainConfig->getId());
			if ($description !== null && $domainConfig->getId() !== $this->selectedDomain->getId()) {
				$descriptions[] = $description;
			}
		}

		return $descriptions;
	}
}
