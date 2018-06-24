<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory;
use Shopsys\FrameworkBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\FrameworkBundle\Model\Order\Status\Grid\OrderStatusInlineEdit;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusFacade;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderStatusController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory
     */
    private $confirmDeleteResponseFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Status\Grid\OrderStatusInlineEdit
     */
    private $orderStatusInlineEdit;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusFacade
     */
    private $orderStatusFacade;

    public function __construct(
        OrderStatusFacade $orderStatusFacade,
        OrderStatusInlineEdit $orderStatusInlineEdit,
        ConfirmDeleteResponseFactory $confirmDeleteResponseFactory
    ) {
        $this->orderStatusFacade = $orderStatusFacade;
        $this->orderStatusInlineEdit = $orderStatusInlineEdit;
        $this->confirmDeleteResponseFactory = $confirmDeleteResponseFactory;
    }

    /**
     * @Route("/order-status/list/")
     */
    public function listAction()
    {
        $grid = $this->orderStatusInlineEdit->getGrid();

        return $this->render('@ShopsysFramework/Admin/Content/OrderStatus/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }

    /**
     * @Route("/order-status/delete/{id}", requirements={"id" = "\d+"})
     * @CsrfProtection
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     */
    public function deleteAction(Request $request, $id)
    {
        $newId = $request->get('newId');

        try {
            $orderStatus = $this->orderStatusFacade->getById($id);
            $this->orderStatusFacade->deleteById($id, $newId);

            if ($newId === null) {
                $this->getFlashMessageSender()->addSuccessFlashTwig(
                    t('Status of orders <strong>{{ name }}</strong> deleted'),
                    [
                        'name' => $orderStatus->getName(),
                    ]
                );
            } else {
                $newOrderStatus = $this->orderStatusFacade->getById($newId);
                $this->getFlashMessageSender()->addSuccessFlashTwig(
                    t('Status of orders <strong>{{ oldName }}</strong> replaced by status <strong>{{ newName }}</strong> and deleted.'),
                    [
                        'oldName' => $orderStatus->getName(),
                        'newName' => $newOrderStatus->getName(),
                    ]
                );
            }
        } catch (\Shopsys\FrameworkBundle\Model\Order\Status\Exception\OrderStatusDeletionForbiddenException $e) {
            $this->getFlashMessageSender()->addErrorFlashTwig(
                t('Status of orders <strong>{{ name }}</strong> reserved and can\'t be deleted'),
                [
                    'name' => $e->getOrderStatus()->getName(),
                ]
            );
        } catch (\Shopsys\FrameworkBundle\Model\Order\Status\Exception\OrderStatusNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected order status doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_orderstatus_list');
    }

    /**
     * @Route("/order-status/delete-confirm/{id}", requirements={"id" = "\d+"})
     * @param int $id
     */
    public function deleteConfirmAction($id)
    {
        try {
            $orderStatus = $this->orderStatusFacade->getById($id);
            if ($this->orderStatusFacade->isOrderStatusUsed($orderStatus)) {
                $message = t(
                    'Because status "%name%"  is used with other orders also, you have to choose a new status which will replace '
                    . 'the existing one. Which status you want to set to these orders? When changing this, there won\'t be emails '
                    . 'sent to customers.',
                    ['%name%' => $orderStatus->getName()]
                );

                return $this->confirmDeleteResponseFactory->createSetNewAndDeleteResponse(
                    $message,
                    'admin_orderstatus_delete',
                    $id,
                    $this->orderStatusFacade->getAllExceptId($id)
                );
            } else {
                $message = t(
                    'Do you really want to remove status of orders "%name%" permanently? It is not used anywhere.',
                    ['%name%' => $orderStatus->getName()]
                );

                return $this->confirmDeleteResponseFactory->createDeleteResponse($message, 'admin_orderstatus_delete', $id);
            }
        } catch (\Shopsys\FrameworkBundle\Model\Order\Status\Exception\OrderStatusNotFoundException $ex) {
            return new Response(t('Selected order status doesn\'t exist.'));
        }
    }
}
