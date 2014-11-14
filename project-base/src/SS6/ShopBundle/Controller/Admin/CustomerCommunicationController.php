<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use SS6\ShopBundle\Form\Admin\CustomerCommunication\CustomerCommunicationFormType;
use SS6\ShopBundle\Model\Setting\Setting;
use SS6\ShopBundle\Model\Setting\SettingValue;

class CustomerCommunicationController extends Controller {

	/**
	 * @Route("/customer_communication/")
	 */
	public function indexAction(Request $request) {
		return $this->render('@SS6Shop/Admin/Content/CustomerCommunication/index.html.twig');
	}

	/**
	 * @Route("/customer_communication/order_submitted/")
	 */
	public function orderSubmittedAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$setting = $this->get('ss6.shop.setting');
		/* @var $setting \SS6\ShopBundle\Model\Setting\Setting */
		$selectedDomain = $this->get('ss6.shop.domain.selected_domain');
		/* @var $selectedDomain \SS6\ShopBundle\Model\Domain\SelectedDomain */

		$data = $setting->get(Setting::ORDER_SUBMITTED_SETTING_NAME, $selectedDomain->getId());
		$form = $this->createForm(new CustomerCommunicationFormType());

		$form->setData(array('content' => $data));
		$form->handleRequest($request);

		if ($form->isValid()) {
			$formData = $form->getData();
			$setting->set(Setting::ORDER_SUBMITTED_SETTING_NAME, $formData['content'], $selectedDomain->getId());

			$flashMessageSender->addSuccess('Nastavení textu po potvrzení objednávky bylo upraveno');
			return $this->redirect($this->generateUrl('admin_customercommunication_ordersubmitted'));
		}

		return $this->render('@SS6Shop/Admin/Content/CustomerCommunication/orderSubmitted.html.twig', array(
			'form' => $form->createView(),
		));
	}

}
