<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Seo\SeoSettingFormTypeFactory;
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
		$seoSettingFormTypeFactory = $this->get(SeoSettingFormTypeFactory::class);
		/* @var $seoSettingFormTypeFactory  \SS6\ShopBundle\Form\Admin\Seo\SeoSettingFormTypeFactory */
		$seoSettingFacade = $this->get(SeoSettingFacade::class);
		/* @var $seoSettingFacade \SS6\ShopBundle\Model\Seo\SeoSettingFacade */
		$selectedDomain = $this->get(SelectedDomain::class);
		/* @var $selectedDomain \SS6\ShopBundle\Model\Domain\SelectedDomain */
		$selectedDomainId = $selectedDomain->getId();

		$form = $this->createForm($seoSettingFormTypeFactory->create());

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

			return $this->redirect($this->generateUrl('admin_seo_index'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Admin/Content/Seo/seoSetting.html.twig', [
			'form' => $form->createView(),
		]);
	}
}
