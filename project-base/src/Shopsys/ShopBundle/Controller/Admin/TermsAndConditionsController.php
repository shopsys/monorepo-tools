<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Form\Admin\TermsAndConditions\TermsAndConditionsSettingFormTypeFactory;
use Shopsys\ShopBundle\Model\TermsAndConditions\TermsAndConditionsFacade;
use Symfony\Component\HttpFoundation\Request;

class TermsAndConditionsController extends AdminBaseController {

	/**
	 * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \Shopsys\ShopBundle\Form\Admin\TermsAndConditions\TermsAndConditionsSettingFormTypeFactory
	 */
	private $termsAndConditionsSettingFormTypeFactory;

	/**
	 * @var \Shopsys\ShopBundle\Model\TermsAndConditions\TermsAndConditionsFacade
	 */
	private $termsAndConditionsFacade;

	public function __construct(
		SelectedDomain $selectedDomain,
		TermsAndConditionsSettingFormTypeFactory $termsAndConditionsSettingFormTypeFactory,
		TermsAndConditionsFacade $termsAndConditionsFacade
	) {
		$this->selectedDomain = $selectedDomain;
		$this->termsAndConditionsSettingFormTypeFactory = $termsAndConditionsSettingFormTypeFactory;
		$this->termsAndConditionsFacade = $termsAndConditionsFacade;
	}

	/**
	 * @Route("/terms-and-conditions/setting/")
	 */
	public function settingAction(Request $request) {
		$selectedDomainId = $this->selectedDomain->getId();
		$termsAndConditionsArticle = $this->termsAndConditionsFacade->findTermsAndConditionsArticleByDomainId($selectedDomainId);

		$termsAndConditionsSettingData = [
			'termsAndConditionsArticle' => $termsAndConditionsArticle,
		];

		$form = $this->createForm($this->termsAndConditionsSettingFormTypeFactory->createForDomain($selectedDomainId));
		$form->setData($termsAndConditionsSettingData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$termsAndConditionsArticle = $form->getData()['termsAndConditionsArticle'];

			$this->termsAndConditionsFacade->setTermsAndConditionsArticleOnDomain(
				$termsAndConditionsArticle,
				$selectedDomainId
			);

			$this->getFlashMessageSender()->addSuccessFlashTwig(t('Terms and conditions settings modified.'));
			return $this->redirectToRoute('admin_termsandconditions_setting');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
		}

		return $this->render('@ShopsysShop/Admin/Content/TermsAndConditions/setting.html.twig', [
			'form' => $form->createView(),
		]);
	}
}
