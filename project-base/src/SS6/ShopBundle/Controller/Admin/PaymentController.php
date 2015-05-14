<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends Controller {

	/**
	 * @var \Symfony\Component\Translation\Translator
	 */
	private $translator;

	public function __construct(Translator $translator) {
		$this->translator = $translator;
	}

	/**
	 * @Route("/payment/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$paymentEditFormTypeFactory = $this->get('ss6.shop.form.admin.payment.payment_edit_form_type_factory');
		/* @var $paymentEditFormTypeFactory \SS6\ShopBundle\Form\Admin\Payment\PaymentEditFormTypeFactory */
		$paymentEditDataFactory = $this->get('ss6.shop.payment.payment_edit_data_factory');
		/* @var $paymentEditDataFactory \SS6\ShopBundle\Model\Payment\PaymentEditDataFactory */
		$currencyFacade = $this->get('ss6.shop.pricing.currency.currency_facade');
		/* @var $currencyFacade \SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade */

		$paymentEditData = $paymentEditDataFactory->createDefault();

		$form = $this->createForm($paymentEditFormTypeFactory->create(), $paymentEditData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$paymentEditFacade = $this->get('ss6.shop.payment.payment_edit_facade');
			/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */
			$payment = $paymentEditFacade->create($paymentEditData);

			$flashMessageSender->addSuccessFlashTwig('Byla vytvořena platba'
					. ' <strong><a href="{{ url }}">{{ name }}</a></strong>', [
				'name' => $payment->getName(),
				'url' => $this->generateUrl('admin_payment_edit', ['id' => $payment->getId()]),
			]);
			return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Admin/Content/Payment/new.html.twig', [
			'form' => $form->createView(),
			'currencies' => $currencyFacade->getAllIndexedById(),
		]);
	}

	/**
	 * @Route("/payment/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$paymentEditFacade = $this->get('ss6.shop.payment.payment_edit_facade');
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */
		$paymentEditFormTypeFactory = $this->get('ss6.shop.form.admin.payment.payment_edit_form_type_factory');
		/* @var $paymentEditFormTypeFactory \SS6\ShopBundle\Form\Admin\Payment\PaymentEditFormTypeFactory */
		$paymentDetailFactory = $this->get('ss6.shop.payment.payment_detail_factory');
		/* @var $paymentDetailFactory \SS6\ShopBundle\Model\Payment\Detail\PaymentDetailFactory */
		$paymentEditDataFactory = $this->get('ss6.shop.payment.payment_edit_data_factory');
		/* @var $paymentEditDataFactory \SS6\ShopBundle\Model\Payment\PaymentEditDataFactory */
		$currencyFacade = $this->get('ss6.shop.pricing.currency.currency_facade');
		/* @var $currencyFacade \SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade */

		$payment = $paymentEditFacade->getByIdWithTransports($id);

		$paymentEditData = $paymentEditDataFactory->createFromPayment($payment);

		$form = $this->createForm($paymentEditFormTypeFactory->create(), $paymentEditData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$paymentEditFacade->edit($payment, $paymentEditData);

			$flashMessageSender->addSuccessFlashTwig('Byla upravena platba'
					. ' <strong><a href="{{ url }}">{{ name }}</a></strong>', [
				'name' => $payment->getName(),
				'url' => $this->generateUrl('admin_payment_edit', ['id' => $payment->getId()]),
			]);
			return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$breadcrumb = $this->get('ss6.shop.admin_navigation.breadcrumb');
		/* @var $breadcrumb \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb */
		$breadcrumb->replaceLastItem(new MenuItem($this->translator->trans('Editace platby - ') . $payment->getName()));

		return $this->render('@SS6Shop/Admin/Content/Payment/edit.html.twig', [
			'form' => $form->createView(),
			'paymentDetail' => $paymentDetailFactory->createDetailForPayment($payment),
			'currencies' => $currencyFacade->getAllIndexedById(),
		]);
	}

	/**
	 * @Route("/payment/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$paymentEditFacade = $this->get('ss6.shop.payment.payment_edit_facade');
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */

		try {
			$paymentName = $paymentEditFacade->getById($id)->getName();
			$paymentEditFacade->deleteById($id);

			$flashMessageSender->addSuccessFlashTwig('Platba <strong>{{ name }}</strong> byla smazána', [
				'name' => $paymentName,
			]);
		} catch (\SS6\ShopBundle\Model\Payment\Exception\PaymentNotFoundException $ex) {
			$flashMessageSender->addErrorFlash('Zvolená platba neexistuje.');
		}

		return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
	}

	public function listAction() {
		$paymentGridFactory = $this->get('ss6.shop.payment.grid.payment_grid_factory');
		/* @var $paymentGridFactory \SS6\ShopBundle\Model\Payment\Grid\PaymentGridFactory */

		$grid = $paymentGridFactory->create();

		return $this->render('@SS6Shop/Admin/Content/Payment/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

}
