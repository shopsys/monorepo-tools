<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Domain\SelectedDomain;
use SS6\ShopBundle\Form\Admin\Heureka\HeurekaShopCertificationFormType;
use SS6\ShopBundle\Model\Heureka\HeurekaFacade;
use SS6\ShopBundle\Model\Heureka\HeurekaSetting;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HeurekaController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\Heureka\HeurekaSetting
	 */
	private $heurekaSetting;

	/**
	 * @var \SS6\ShopBundle\Model\Heureka\HeurekaFacade
	 */
	private $heurekaFacade;

	public function __construct(SelectedDomain $selectedDomain, HeurekaSetting $heurekaSetting, HeurekaFacade $heurekaFacade) {
		$this->selectedDomain = $selectedDomain;
		$this->heurekaSetting = $heurekaSetting;
		$this->heurekaFacade = $heurekaFacade;
	}

	/**
	 * @Route("/heureka/setting/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function settingAction(Request $request) {
		$selectedDomainId = $this->selectedDomain->getId();
		$selectedDomainConfig = $this->selectedDomain->getCurrentSelectedDomain();
		$locale = $selectedDomainConfig->getLocale();
		$formView = null;

		if ($this->heurekaFacade->isDomainLocaleSupported($locale)) {
			$form = $this->createForm(new HeurekaShopCertificationFormType());
			$heurekaShopCertificationData = [];

			$heurekaShopCertificationData['heurekaApiKey'] = $this->heurekaSetting->getApiKeyByDomainId($selectedDomainId);
			$heurekaShopCertificationData['heurekaWidgetCode'] = $this->heurekaSetting->getHeurekaShopCertificationWidgetByDomainId(
				$selectedDomainId
			);
			$form->setData($heurekaShopCertificationData);
			$form->handleRequest($request);

			if ($form->isValid()) {
				$heurekaShopCertificationData = $form->getData();

				$this->heurekaSetting->setApiKeyForDomain($heurekaShopCertificationData['heurekaApiKey'], $selectedDomainId);
				$this->heurekaSetting->setHeurekaShopCertificationWidgetForDomain(
					$heurekaShopCertificationData['heurekaWidgetCode'],
					$selectedDomainId
				);

				$this->getFlashMessageSender()->addSuccessFlash(t('Settings modified.'));
			}
			$formView = $form->createView();
		}

		return $this->render('@SS6Shop/Admin/Content/Heureka/setting.html.twig', [
			'form' => $formView,
			'serverName' => $this->heurekaFacade->getServerNameByLocale($locale),
			'selectedDomainConfig' => $selectedDomainConfig,
		]);
	}

	public function embedWidgetAction() {
		$domainId = $this->selectedDomain->getId();

		if (!$this->heurekaFacade->isHeurekaWidgetActivated($domainId)) {
			return new Response('');
		}

		return $this->render('@SS6Shop/Admin/Content/Heureka/widget.html.twig', [
			'widgetCode' => $this->heurekaSetting->getHeurekaShopCertificationWidgetByDomainId($domainId),
		]);
	}
}
