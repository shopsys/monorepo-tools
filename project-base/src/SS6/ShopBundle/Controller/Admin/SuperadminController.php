<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Superadmin\InputPriceTypeFormType;
use SS6\ShopBundle\Model\Grid\ArrayDataSource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;

class SuperadminController extends Controller {

	/**
	 * @Route("/superadmin/")
	 */
	public function indexAction() {
		return $this->render('@SS6Shop/Admin/Content/Superadmin/index.html.twig');
	}

	/**
	 * @Route("/superadmin/icons/")
	 */
	public function iconsAction() {
		return $this->render('@SS6Shop/Admin/Content/Superadmin/icons.html.twig');
	}

	/**
	 * @Route("/superadmin/icons/{icon}/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function iconDetailAction($icon) {
		return $this->render('@SS6Shop/Admin/Content/Superadmin/iconDetail.html.twig', [
			'icon' => $icon,
		]);
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
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$pricingSetting = $this->get('ss6.shop.pricing.pricing_setting');
		/* @var $pricingSetting \SS6\ShopBundle\Model\Pricing\PricingSetting */
		$pricingSettingFacade = $this->get('ss6.shop.pricing.pricing_setting_facade');
		/* @var $pricingSettingFacade \SS6\ShopBundle\Model\Pricing\PricingSettingFacade */
		$translator = $this->get('translator');
		/* @var $translator \Symfony\Component\Translation\TranslatorInterface */

		$form = $this->createForm(new InputPriceTypeFormType($translator));

		$pricingSettingData = [];
		if (!$form->isSubmitted()) {
			$pricingSettingData['type'] = $pricingSetting->getInputPriceType();
		}

		$form->setData($pricingSettingData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$pricingSettingData = $form->getData();
			$pricingSettingFacade->setInputPriceType($pricingSettingData['type']);

			$flashMessageSender->addSuccessFlashTwig('<strong><a href="{{ url }}">Nastavení cenotvorby</a></strong> bylo upraveno', [
				'url' => $this->generateUrl('admin_superadmin_pricing'),
			]);
			return $this->redirect($this->generateUrl('admin_superadmin_index'));
		}

		return $this->render('@SS6Shop/Admin/Content/Superadmin/pricing.html.twig', [
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/superadmin/urls/")
	 */
	public function urlsAction() {
		$gridFactory = $this->get('ss6.shop.grid.factory');
		/* @var $gridFactory \SS6\ShopBundle\Model\Grid\GridFactory */
		$localization = $this->get('ss6.shop.localization.localization');
		/* @var $localization \SS6\ShopBundle\Model\Localization\Localization */

		$allLocales = $localization->getAllLocales();
		$dataSource = new ArrayDataSource($this->loadDataForUrls($allLocales));

		$grid = $gridFactory->create('urlsList', $dataSource);

		foreach ($allLocales as $locale) {
			$grid->addColumn($locale, $locale, $localization->getLanguageName($locale));
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
		$localizedRouterFactory = $this->get('ss6.shop.router.localized_router_factory');
		/* @var $localizedRouterFactory \SS6\ShopBundle\Component\Router\LocalizedRouterFactory */

		$data = [];
		$requestContext = new RequestContext();
		foreach ($locales as $locale) {
			$rowIndex = 0;
			$allRoutes = $localizedRouterFactory->getRouter($locale, $requestContext)
				->getRouteCollection()
				->all();
			foreach ($allRoutes as $route) {
				$data[$rowIndex][$locale] = $route->getPath();
				$rowIndex++;
			}
		}

		return $data;
	}
}
