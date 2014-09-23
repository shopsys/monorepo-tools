<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Superadmin\InputPriceTypeFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
		return $this->render('@SS6Shop/Admin/Content/Superadmin/iconDetail.html.twig', array(
			'icon' => $icon
		));
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
		$flashMessageTwig = $this->get('ss6.shop.flash_message.twig_sender.admin');
		/* @var $flashMessageTwig \SS6\ShopBundle\Model\FlashMessage\TwigSender */
		$pricingSetting = $this->get('ss6.shop.pricing.pricing_setting');
		/* @var $pricingSetting \SS6\ShopBundle\Model\Pricing\PricingSetting */

		$form = $this->createForm(new InputPriceTypeFormType());

		$pricingSettingData = array();
		if (!$form->isSubmitted()) {
			$pricingSettingData['type'] = $pricingSetting->getInputPriceType();
		}

		$form->setData($pricingSettingData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$pricingSettingData = $form->getData();

			$pricingSettingFacade = $this->get('ss6.shop.pricing.pricing_setting_facade');
			/* @var $pricingSettingFacade \SS6\ShopBundle\Model\Pricing\PricingSettingFacade */
			$pricingSettingFacade->edit($pricingSettingData);

			$flashMessageTwig->addSuccess('<strong><a href="{{ url }}">Nastavení cenotvorby</a></strong> bylo upraveno', array(
				'url' => $this->generateUrl('admin_superadmin_pricing'),
			));
			return $this->redirect($this->generateUrl('admin_superadmin_index'));
		}

		return $this->render('@SS6Shop/Admin/Content/Superadmin/pricing.html.twig', array(
			'form' => $form->createView(),
		));
	}

}
