<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\ShopBundle\Model\Administrator\AdministratorGridFacade;
use Shopsys\ShopBundle\Model\Order\PromoCode\Grid\PromoCodeInlineEdit;
use Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeFacade;

class PromoCodeController extends AdminBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeFacade
     */
    private $promoCodeFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\PromoCode\Grid\PromoCodeInlineEdit
     */
    private $promoCodeInlineEdit;

    /**
     * @var \Shopsys\ShopBundle\Model\Administrator\AdministratorGridFacade
     */
    private $administratorGridFacade;

    public function __construct(
        PromoCodeFacade $promoCodeFacade,
        PromoCodeInlineEdit $promoCodeInlineEdit,
        AdministratorGridFacade $administratorGridFacade
    ) {
        $this->promoCodeFacade = $promoCodeFacade;
        $this->promoCodeInlineEdit = $promoCodeInlineEdit;
        $this->administratorGridFacade = $administratorGridFacade;
    }

    /**
     * @Route("/promo-code/list")
     */
    public function listAction()
    {
        $administrator = $this->getUser();
        /* @var $administrator \Shopsys\ShopBundle\Model\Administrator\Administrator */

        $grid = $this->promoCodeInlineEdit->getGrid();

        $grid->enablePaging();

        $this->administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

        return $this->render('@ShopsysShop/Admin/Content/PromoCode/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }

    /**
     * @Route("/promo-code/delete/{id}", requirements={"id" = "\d+"})
     * @CsrfProtection
     * @param int $id
     */
    public function deleteAction($id)
    {
        try {
            $code = $this->promoCodeFacade->getById($id)->getCode();

            $this->promoCodeFacade->deleteById($id);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Discount coupon <strong>{{ code }}</strong> deleted.'),
                [
                    'code' => $code,
                ]
            );
        } catch (\Shopsys\ShopBundle\Model\Order\PromoCode\Exception\PromoCodeNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected discount coupon doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_promocode_list');
    }
}
