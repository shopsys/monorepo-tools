<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Form\Admin\Seo\SeoSettingFormTypeFactory;
use Shopsys\ShopBundle\Model\Seo\SeoSettingFacade;
use Symfony\Component\HttpFoundation\Request;

class SeoController extends AdminBaseController {

	/**
	 * @var \Shopsys\ShopBundle\Form\Admin\Seo\SeoSettingFormTypeFactory
	 */
	private $seoSettingFormTypeFactory;

	/**
	 * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \Shopsys\ShopBundle\Model\Seo\SeoSettingFacade
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
		$seoSettingData['title'] = $this->seoSettingFacade->getTitleMainPage($selectedDomainId);
		$seoSettingData['metaDescription'] = $this->seoSettingFacade->getDescriptionMainPage($selectedDomainId);
		$seoSettingData['titleAddOn'] = $this->seoSettingFacade->getTitleAddOn($selectedDomainId);

		$form->setData($seoSettingData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$seoSettingData = $form->getData();
			$this->seoSettingFacade->setTitleMainPage($seoSettingData['title'], $selectedDomainId);
			$this->seoSettingFacade->setDescriptionMainPage($seoSettingData['metaDescription'], $selectedDomainId);
			$this->seoSettingFacade->setTitleAddOn($seoSettingData['titleAddOn'], $selectedDomainId);

			$this->getFlashMessageSender()->addSuccessFlash(t('SEO attributes settings modified'));

			return $this->redirectToRoute('admin_seo_index');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
		}

		return $this->render('@ShopsysShop/Admin/Content/Seo/seoSetting.html.twig', [
			'form' => $form->createView(),
		]);
	}
}
