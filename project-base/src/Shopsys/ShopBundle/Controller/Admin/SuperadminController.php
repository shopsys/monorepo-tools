<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Grid\ArrayDataSource;
use SS6\ShopBundle\Component\Grid\GridFactory;
use SS6\ShopBundle\Component\Router\LocalizedRouterFactory;
use SS6\ShopBundle\Form\Admin\Module\ModulesFormType;
use SS6\ShopBundle\Form\Admin\Superadmin\InputPriceTypeFormType;
use SS6\ShopBundle\Model\Localization\Localization;
use SS6\ShopBundle\Model\Module\ModuleFacade;
use SS6\ShopBundle\Model\Module\ModuleList;
use SS6\ShopBundle\Model\Pricing\DelayedPricingSetting;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;

class SuperadminController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Module\ModuleList
	 */
	private $moduleList;

	/**
	 * @var \SS6\ShopBundle\Model\Module\ModuleFacade
	 */
	private $moduleFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Router\LocalizedRouterFactory
	 */
	private $localizedRouterFactory;

	/**
	 * @var \SS6\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PricingSetting
	 */
	private $pricingSetting;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\DelayedPricingSetting
	 */
	private $delayedPricingSetting;

	public function __construct(
		ModuleList $moduleList,
		ModuleFacade $moduleFacade,
		PricingSetting $pricingSetting,
		DelayedPricingSetting $delayedPricingSetting,
		GridFactory $gridFactory,
		Localization $localization,
		LocalizedRouterFactory $localizedRouterFactory
	) {
		$this->moduleList = $moduleList;
		$this->moduleFacade = $moduleFacade;
		$this->pricingSetting = $pricingSetting;
		$this->delayedPricingSetting = $delayedPricingSetting;
		$this->gridFactory = $gridFactory;
		$this->localization = $localization;
		$this->localizedRouterFactory = $localizedRouterFactory;
	}

	/**
	 * @Route("/superadmin/errors/")
	 */
	public function errorsAction() {
		return $this->render('@SS6Shop/Admin/Content/Superadmin/errors.html.twig');
	}

	/**
	 * @Route("/superadmin/pricing/")
	 */
	public function pricingAction(Request $request) {
		$form = $this->createForm(new InputPriceTypeFormType());

		$pricingSettingData = [];
		$pricingSettingData['type'] = $this->pricingSetting->getInputPriceType();

		$form->setData($pricingSettingData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$pricingSettingData = $form->getData();

			$this->delayedPricingSetting->scheduleSetInputPriceType($pricingSettingData['type']);

			$this->getFlashMessageSender()->addSuccessFlash(t('Pricing settings modified'));
			return $this->redirectToRoute('admin_superadmin_pricing');
		}

		return $this->render('@SS6Shop/Admin/Content/Superadmin/pricing.html.twig', [
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/superadmin/urls/")
	 */
	public function urlsAction() {
		$allLocales = $this->localization->getAllLocales();
		$dataSource = new ArrayDataSource($this->loadDataForUrls($allLocales));

		$grid = $this->gridFactory->create('urlsList', $dataSource);

		foreach ($allLocales as $locale) {
			$grid->addColumn($locale, $locale, $this->localization->getLanguageName($locale));
		}

		return $this->render('@SS6Shop/Admin/Content/Superadmin/urlsListGrid.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @param array $locales
	 * @return array
	 */
	private function loadDataForUrls(array $locales) {
		$data = [];
		$requestContext = new RequestContext();
		foreach ($locales as $locale) {
			$rowIndex = 0;
			$allRoutes = $this->localizedRouterFactory->getRouter($locale, $requestContext)
				->getRouteCollection()
				->all();
			foreach ($allRoutes as $route) {
				$data[$rowIndex][$locale] = $route->getPath();
				$rowIndex++;
			}
		}

		return $data;
	}

	/**
	 * @Route("/superadmin/modules/")
	 */
	public function modulesAction(Request $request) {
		$form = $this->createForm(new ModulesFormType($this->moduleList));

		$formData = [];
		foreach ($this->moduleList->getValues() as $moduleName) {
			$formData['modules'][$moduleName] = $this->moduleFacade->isEnabled($moduleName);
		}

		$form->setData($formData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$formData = $form->getData();

			foreach ($formData[ModulesFormType::MODULES_SUBFORM_NAME] as $moduleName => $isEnabled) {
				$this->moduleFacade->setEnabled($moduleName, $isEnabled);
			}

			$this->getFlashMessageSender()->addSuccessFlash(t('Modules configuration modified'));
			return $this->redirectToRoute('admin_superadmin_modules');
		}

		return $this->render('@SS6Shop/Admin/Content/Superadmin/modules.html.twig', [
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/superadmin/css-documentation/")
	 */
	public function cssDocumentationAction() {
		return $this->render('@SS6Shop/Admin/Content/Superadmin/cssDocumentation.html.twig');
	}

}
