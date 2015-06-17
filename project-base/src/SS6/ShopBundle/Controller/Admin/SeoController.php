<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Controller\Admin\BaseController;
use SS6\ShopBundle\Form\Admin\Seo\SeoSettingFormTypeFactory;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Seo\SeoSettingFacade;
use Symfony\Component\HttpFoundation\Request;

class SeoController extends BaseController {

	/**
	 * @var \SS6\ShopBundle\Form\Admin\Seo\SeoSettingFormTypeFactory
	 */
	private $seoSettingFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\Seo\SeoSettingFacade
	 */
	private $seoSettingFacade;

	public function __construct(
		SeoSettingFormTypeFactory $seoSettingFormTypeFactory,
		SeoSettingFacade $seoSettingFacade,
		SelectedDomain $selectedDomain
	) {
		$this->seoSettingFormTypeFactory = $seoSettingFormTypeFactory;
		$this->seoSettingFacade = $seoSettingFacade;
		$this->selectedDomain = $selectedDomain;
	}

	/**
	 * @Route("/seo/")
	 */
	public function indexAction(Request $request) {
		$selectedDomainId = $this->selectedDomain->getId();

		$form = $this->createForm($this->seoSettingFormTypeFactory->create());

		$seoSettingData = [];
		if (!$form->isSubmitted()) {
			$seoSettingData['title'] = $this->seoSettingFacade->getTitleMainPage($selectedDomainId);
			$seoSettingData['metaDescription'] = $this->seoSettingFacade->getDescriptionMainPage($selectedDomainId);
			$seoSettingData['titleAddOn'] = $this->seoSettingFacade->getTitleAddOn($selectedDomainId);
		}

		$form->setData($seoSettingData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$seoSettingData = $form->getData();
			$this->seoSettingFacade->setTitleMainPage($seoSettingData['title'], $selectedDomainId);
			$this->seoSettingFacade->setDescriptionMainPage($seoSettingData['metaDescription'], $selectedDomainId);
			$this->seoSettingFacade->setTitleAddOn($seoSettingData['titleAddOn'], $selectedDomainId);

			$this->getFlashMessageSender()->addSuccessFlash('Natavení SEO atributů bylo upraveno');

			return $this->redirect($this->generateUrl('admin_seo_index'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Admin/Content/Seo/seoSetting.html.twig', [
			'form' => $form->createView(),
		]);
	}
}
