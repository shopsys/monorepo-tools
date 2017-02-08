<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\ShopBundle\Form\Admin\Payment\PaymentEditFormTypeFactory;
use Shopsys\ShopBundle\Model\AdminNavigation\Breadcrumb;
use Shopsys\ShopBundle\Model\AdminNavigation\MenuItem;
use Shopsys\ShopBundle\Model\Payment\Detail\PaymentDetailFactory;
use Shopsys\ShopBundle\Model\Payment\Grid\PaymentGridFactory;
use Shopsys\ShopBundle\Model\Payment\PaymentEditDataFactory;
use Shopsys\ShopBundle\Model\Payment\PaymentEditFacade;
use Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends AdminBaseController {

	/**
	 * @var \Shopsys\ShopBundle\Form\Admin\Payment\PaymentEditFormTypeFactory
	 */
	private $paymentEditFormTypeFactory;

	/**
	 * @var \Shopsys\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	/**
	 * @var \Shopsys\ShopBundle\Model\Payment\Detail\PaymentDetailFactory
	 */
	private $paymentDetailFactory;

	/**
	 * @var \Shopsys\ShopBundle\Model\Payment\Grid\PaymentGridFactory
	 */
	private $paymentGridFactory;

	/**
	 * @var \Shopsys\ShopBundle\Model\Payment\PaymentEditDataFactory
	 */
	private $paymentEditDataFactory;

	/**
	 * @var \Shopsys\ShopBundle\Model\Payment\PaymentEditFacade
	 */
	private $paymentEditFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade
	 */
	private $currencyFacade;

	public function __construct(
		PaymentEditFormTypeFactory $paymentEditFormTypeFactory,
		PaymentEditDataFactory $paymentEditDataFactory,
		CurrencyFacade $currencyFacade,
		PaymentEditFacade $paymentEditFacade,
		PaymentDetailFactory $paymentDetailFactory,
		PaymentGridFactory $paymentGridFactory,
		Breadcrumb $breadcrumb
	) {
		$this->paymentEditFormTypeFactory = $paymentEditFormTypeFactory;
		$this->paymentEditDataFactory = $paymentEditDataFactory;
		$this->currencyFacade = $currencyFacade;
		$this->paymentEditFacade = $paymentEditFacade;
		$this->paymentDetailFactory = $paymentDetailFactory;
		$this->paymentGridFactory = $paymentGridFactory;
		$this->breadcrumb = $breadcrumb;
	}

	/**
	 * @Route("/payment/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$form = $this->createForm($this->paymentEditFormTypeFactory->create());
		$paymentEditData = $this->paymentEditDataFactory->createDefault();

		$form->setData($paymentEditData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$payment = $this->paymentEditFacade->create($paymentEditData);

			$this->getFlashMessageSender()->addSuccessFlashTwig(
				t('Payment <strong><a href="{{ url }}">{{ name }}</a></strong> created'),
				[
					'name' => $payment->getName(),
					'url' => $this->generateUrl('admin_payment_edit', ['id' => $payment->getId()]),
				]
			);
			return $this->redirectToRoute('admin_transportandpayment_list');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
		}

		return $this->render('@ShopsysShop/Admin/Content/Payment/new.html.twig', [
			'form' => $form->createView(),
			'currencies' => $this->currencyFacade->getAllIndexedById(),
		]);
	}

	/**
	 * @Route("/payment/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$payment = $this->paymentEditFacade->getByIdWithTransports($id);

		$form = $this->createForm($this->paymentEditFormTypeFactory->create());

		$paymentEditData = $this->paymentEditDataFactory->createFromPayment($payment);

		$form->setData($paymentEditData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$this->paymentEditFacade->edit($payment, $paymentEditData);

			$this->getFlashMessageSender()->addSuccessFlashTwig(
				t('Payment <strong><a href="{{ url }}">{{ name }}</a></strong> modified'),
				[
					'name' => $payment->getName(),
					'url' => $this->generateUrl('admin_payment_edit', ['id' => $payment->getId()]),
				]
			);
			return $this->redirectToRoute('admin_transportandpayment_list');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
		}

		$this->breadcrumb->overrideLastItem(new MenuItem(t('Editing payment - %name%', ['%name%' => $payment->getName()])));

		return $this->render('@ShopsysShop/Admin/Content/Payment/edit.html.twig', [
			'form' => $form->createView(),
			'paymentDetail' => $this->paymentDetailFactory->createDetailForPayment($payment),
			'currencies' => $this->currencyFacade->getAllIndexedById(),
		]);
	}

	/**
	 * @Route("/payment/delete/{id}", requirements={"id" = "\d+"})
	 * @CsrfProtection
	 * @param int $id
	 */
	public function deleteAction($id) {
		try {
			$paymentName = $this->paymentEditFacade->getById($id)->getName();

			$this->paymentEditFacade->deleteById($id);

			$this->getFlashMessageSender()->addSuccessFlashTwig(
				t('Payment <strong>{{ name }}</strong> deleted'),
				[
					'name' => $paymentName,
				]
			);
		} catch (\Shopsys\ShopBundle\Model\Payment\Exception\PaymentNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash(t('Selected payment doesn\'t exist.'));
		}

		return $this->redirectToRoute('admin_transportandpayment_list');
	}

	public function listAction() {
		$grid = $this->paymentGridFactory->create();

		return $this->render('@ShopsysShop/Admin/Content/Payment/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

}
