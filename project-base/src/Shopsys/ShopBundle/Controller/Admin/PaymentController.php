<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\ShopBundle\Form\Admin\Payment\PaymentEditFormType;
use Shopsys\ShopBundle\Model\AdminNavigation\Breadcrumb;
use Shopsys\ShopBundle\Model\AdminNavigation\MenuItem;
use Shopsys\ShopBundle\Model\Payment\Detail\PaymentDetailFactory;
use Shopsys\ShopBundle\Model\Payment\Grid\PaymentGridFactory;
use Shopsys\ShopBundle\Model\Payment\PaymentEditDataFactory;
use Shopsys\ShopBundle\Model\Payment\PaymentFacade;
use Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends AdminBaseController
{
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
     * @var \Shopsys\ShopBundle\Model\Payment\PaymentFacade
     */
    private $paymentFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    public function __construct(
        PaymentEditDataFactory $paymentEditDataFactory,
        CurrencyFacade $currencyFacade,
        PaymentFacade $paymentFacade,
        PaymentDetailFactory $paymentDetailFactory,
        PaymentGridFactory $paymentGridFactory,
        Breadcrumb $breadcrumb
    ) {
        $this->paymentEditDataFactory = $paymentEditDataFactory;
        $this->currencyFacade = $currencyFacade;
        $this->paymentFacade = $paymentFacade;
        $this->paymentDetailFactory = $paymentDetailFactory;
        $this->paymentGridFactory = $paymentGridFactory;
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * @Route("/payment/new/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $paymentEditData = $this->paymentEditDataFactory->createDefault();

        $form = $this->createForm(PaymentEditFormType::class, $paymentEditData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $payment = $this->paymentFacade->create($paymentEditData);

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
    public function editAction(Request $request, $id)
    {
        $payment = $this->paymentFacade->getById($id);
        $paymentEditData = $this->paymentEditDataFactory->createFromPayment($payment);

        $form = $this->createForm(PaymentEditFormType::class, $paymentEditData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->paymentFacade->edit($payment, $paymentEditData);

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
    public function deleteAction($id)
    {
        try {
            $paymentName = $this->paymentFacade->getById($id)->getName();

            $this->paymentFacade->deleteById($id);

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

    public function listAction()
    {
        $grid = $this->paymentGridFactory->create();

        return $this->render('@ShopsysShop/Admin/Content/Payment/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }
}
