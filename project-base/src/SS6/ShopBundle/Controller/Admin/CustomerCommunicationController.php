<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Form\Admin\CustomerCommunication\CustomerCommunicationFormType;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Setting\Setting;
use Symfony\Component\HttpFoundation\Request;

class CustomerCommunicationController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\Setting\Setting
	 */
	private $setting;

	public function __construct(
		Setting $setting,
		SelectedDomain $selectedDomain
	) {
		$this->setting = $setting;
		$this->selectedDomain = $selectedDomain;
	}

	/**
	 * @Route("/customer_communication/")
	 */
	public function indexAction() {
		return $this->render('@SS6Shop/Admin/Content/CustomerCommunication/index.html.twig');
	}

	/**
	 * @Route("/customer_communication/order_submitted/")
	 */
	public function orderSubmittedAction(Request $request) {
		$data = $this->setting->get(Setting::ORDER_SUBMITTED_SETTING_NAME, $this->selectedDomain->getId());
		$form = $this->createForm(new CustomerCommunicationFormType());

		$form->setData(['content' => $data]);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$formData = $form->getData();
			$this->setting->set(Setting::ORDER_SUBMITTED_SETTING_NAME, $formData['content'], $this->selectedDomain->getId());

			$this->getFlashMessageSender()->addSuccessFlash('Nastavení textu po potvrzení objednávky bylo upraveno');

			return $this->redirect($this->generateUrl('admin_customercommunication_ordersubmitted'));
		}

		return $this->render('@SS6Shop/Admin/Content/CustomerCommunication/orderSubmitted.html.twig', [
			'form' => $form->createView(),
		]);
	}

}
