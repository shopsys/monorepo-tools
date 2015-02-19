<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Seo\SeoSettingFormType;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Seo\SeoSettingFacade;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SeoController extends Controller {

	/**
	 * @Route("/seo/")
	 */
	public function indexAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$seoSettingFacade = $this->get(SeoSettingFacade::class);
		/* @var $seoSettingFacade \SS6\ShopBundle\Model\Seo\SeoSettingFacade */
		$domain = $this->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */
		$selectedDomain = $this->get(SelectedDomain::class);
		/* @var $selectedDomain \SS6\ShopBundle\Model\Domain\SelectedDomain */
		$selectedDomainId = $selectedDomain->getId();

		$form = $this->createForm(new SeoSettingFormType(
			$this->getTitlesOnOtherDomains($domain, $selectedDomain, $seoSettingFacade),
			$this->getTitleAddOnsOnOtherDomains($domain, $selectedDomain, $seoSettingFacade),
			$this->getDescriptionsOnOtherDomains($domain, $selectedDomain, $seoSettingFacade)
		));

		$seoSettingData = [];
		if (!$form->isSubmitted()) {
			$seoSettingData['title'] = $seoSettingFacade->getTitleMainPage($selectedDomainId);
			$seoSettingData['metaDescription'] = $seoSettingFacade->getDescriptionMainPage($selectedDomainId);
			$seoSettingData['titleAddOn'] = $seoSettingFacade->getTitleAddOn($selectedDomainId);
		}

		$form->setData($seoSettingData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$seoSettingData = $form->getData();
			$seoSettingFacade->setTitleMainPage($seoSettingData['title'], $selectedDomainId);
			$seoSettingFacade->setDescriptionMainPage($seoSettingData['metaDescription'], $selectedDomainId);
			$seoSettingFacade->setTitleAddOn($seoSettingData['titleAddOn'], $selectedDomainId);

			$flashMessageSender->addSuccessFlash('Natavení SEO atributů bylo upraveno');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Admin/Content/Seo/seoSetting.html.twig', [
			'form' => $form->createView(),
		]);
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
