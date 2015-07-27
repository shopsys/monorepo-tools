<?php

namespace SS6\ShopBundle\Controller\Admin;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Controller\Admin\BaseController;
use SS6\ShopBundle\Form\Admin\Payment\PaymentEditFormTypeFactory;
use SS6\ShopBundle\Model\AdminNavigation\Breadcrumb;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Payment\Detail\PaymentDetailFactory;
use SS6\ShopBundle\Model\Payment\Grid\PaymentGridFactory;
use SS6\ShopBundle\Model\Payment\PaymentEditDataFactory;
use SS6\ShopBundle\Model\Payment\PaymentEditFacade;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends BaseController {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Form\Admin\Payment\PaymentEditFormTypeFactory
	 */
	private $paymentEditFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Detail\PaymentDetailFactory
	 */
	private $paymentDetailFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Grid\PaymentGridFactory
	 */
	private $paymentGridFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentEditDataFactory
	 */
	private $paymentEditDataFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentEditFacade
	 */
	private $paymentEditFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade
	 */
	private $currencyFacade;

	/**
	 * @var \Symfony\Component\Translation\Translator
	 */
	private $translator;

	public function __construct(
		Translator $translator,
		PaymentEditFormTypeFactory $paymentEditFormTypeFactory,
		PaymentEditDataFactory $paymentEditDataFactory,
		CurrencyFacade $currencyFacade,
		PaymentEditFacade $paymentEditFacade,
		PaymentDetailFactory $paymentDetailFactory,
		EntityManager $em,
		PaymentGridFactory $paymentGridFactory,
		Breadcrumb $breadcrumb
	) {
		$this->translator = $translator;
		$this->paymentEditFormTypeFactory = $paymentEditFormTypeFactory;
		$this->paymentEditDataFactory = $paymentEditDataFactory;
		$this->currencyFacade = $currencyFacade;
		$this->paymentEditFacade = $paymentEditFacade;
		$this->paymentDetailFactory = $paymentDetailFactory;
		$this->em = $em;
		$this->paymentGridFactory = $paymentGridFactory;
		$this->breadcrumb = $breadcrumb;
	}

	/**
	 * @Route("/payment/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$paymentEditData = $this->paymentEditDataFactory->createDefault();

		$form = $this->createForm($this->paymentEditFormTypeFactory->create(), $paymentEditData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$payment = $this->em->transactional(
				function () use ($paymentEditData) {
					return $this->paymentEditFacade->create($paymentEditData);
				}
			);

			$this->getFlashMessageSender()->addSuccessFlashTwig('Byla vytvořena platba'
					. ' <strong><a href="{{ url }}">{{ name }}</a></strong>', [
				'name' => $payment->getName(),
				'url' => $this->generateUrl('admin_payment_edit', ['id' => $payment->getId()]),
			]);
			return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Admin/Content/Payment/new.html.twig', [
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

		$paymentEditData = $this->paymentEditDataFactory->createFromPayment($payment);

		$form = $this->createForm($this->paymentEditFormTypeFactory->create(), $paymentEditData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$this->em->transactional(
				function () use ($payment, $paymentEditData) {
					$this->paymentEditFacade->edit($payment, $paymentEditData);
				}
			);

			$this->getFlashMessageSender()->addSuccessFlashTwig('Byla upravena platba'
					. ' <strong><a href="{{ url }}">{{ name }}</a></strong>', [
				'name' => $payment->getName(),
				'url' => $this->generateUrl('admin_payment_edit', ['id' => $payment->getId()]),
			]);
			return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$this->breadcrumb->replaceLastItem(new MenuItem($this->translator->trans('Editace platby - ') . $payment->getName()));

		return $this->render('@SS6Shop/Admin/Content/Payment/edit.html.twig', [
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
			$this->em->transactional(
				function () use ($id) {
					$this->paymentEditFacade->deleteById($id);
				}
			);

			$this->getFlashMessageSender()->addSuccessFlashTwig('Platba <strong>{{ name }}</strong> byla smazána', [
				'name' => $paymentName,
			]);
		} catch (\SS6\ShopBundle\Model\Payment\Exception\PaymentNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash('Zvolená platba neexistuje.');
		}

		return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
	}

	public function listAction() {
		$grid = $this->paymentGridFactory->create();

		return $this->render('@SS6Shop/Admin/Content/Payment/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

}
