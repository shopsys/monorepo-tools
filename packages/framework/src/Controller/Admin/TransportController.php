<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Controller\AdminBaseController;
use Shopsys\FrameworkBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\FrameworkBundle\Form\Admin\Transport\TransportEditFormType;
use Shopsys\FrameworkBundle\Model\AdminNavigation\Breadcrumb;
use Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\FrameworkBundle\Model\Transport\Grid\TransportGridFactory;
use Shopsys\FrameworkBundle\Model\Transport\TransportDataFactory;
use Shopsys\FrameworkBundle\Model\Transport\TransportFacade;
use Symfony\Component\HttpFoundation\Request;

class TransportController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\AdminNavigation\Breadcrumb
     */
    private $breadcrumb;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\Grid\TransportGridFactory
     */
    private $transportGridFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportDataFactory
     */
    private $transportDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportFacade
     */
    private $transportFacade;

    public function __construct(
        TransportFacade $transportFacade,
        TransportGridFactory $transportGridFactory,
        TransportDataFactory $transportDataFactory,
        CurrencyFacade $currencyFacade,
        Breadcrumb $breadcrumb
    ) {
        $this->transportFacade = $transportFacade;
        $this->transportGridFactory = $transportGridFactory;
        $this->transportDataFactory = $transportDataFactory;
        $this->currencyFacade = $currencyFacade;
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * @Route("/transport/new/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $transportData = $this->transportDataFactory->createDefault();

        $form = $this->createForm(TransportEditFormType::class, $transportData, [
            'transport' => null,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transport = $this->transportFacade->create($form->getData());

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Shipping <strong><a href="{{ url }}">{{ name }}</a></strong> created'),
                [
                'name' => $transport->getName(),
                'url' => $this->generateUrl('admin_transport_edit', ['id' => $transport->getId()]),
                ]
            );
            return $this->redirectToRoute('admin_transportandpayment_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        return $this->render('@ShopsysFramework/Admin/Content/Transport/new.html.twig', [
            'form' => $form->createView(),
            'currencies' => $this->currencyFacade->getAllIndexedById(),
        ]);
    }

    /**
     * @Route("/transport/edit/{id}", requirements={"id" = "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     */
    public function editAction(Request $request, $id)
    {
        $transport = $this->transportFacade->getById($id);
        $transportData = $this->transportDataFactory->createFromTransport($transport);

        $form = $this->createForm(TransportEditFormType::class, $transportData, [
            'transport' => $transport,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->transportFacade->edit($transport, $transportData);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Shipping <strong><a href="{{ url }}">{{ name }}</a></strong> was modified'),
                [
                    'name' => $transport->getName(),
                    'url' => $this->generateUrl('admin_transport_edit', ['id' => $transport->getId()]),
                ]
            );
            return $this->redirectToRoute('admin_transportandpayment_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlash(t('Please check the correctness of all data filled.'));
        }

        $this->breadcrumb->overrideLastItem(new MenuItem(t('Editing shipping - %name%', ['%name%' => $transport->getName()])));

        return $this->render('@ShopsysFramework/Admin/Content/Transport/edit.html.twig', [
            'form' => $form->createView(),
            'transport' => $transport,
            'currencies' => $this->currencyFacade->getAllIndexedById(),
        ]);
    }

    /**
     * @Route("/transport/delete/{id}", requirements={"id" = "\d+"})
     * @CsrfProtection
     * @param int $id
     */
    public function deleteAction($id)
    {
        try {
            $transportName = $this->transportFacade->getById($id)->getName();

            $this->transportFacade->deleteById($id);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Shipping <strong>{{ name }}</strong> deleted'),
                [
                    'name' => $transportName,
                ]
            );
        } catch (\Shopsys\FrameworkBundle\Model\Transport\Exception\TransportNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected shipping doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_transportandpayment_list');
    }

    public function listAction()
    {
        $grid = $this->transportGridFactory->create();

        return $this->render('@ShopsysFramework/Admin/Content/Transport/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }
}
