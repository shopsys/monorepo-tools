<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\FrameworkBundle\Model\Product\Flag\FlagFacade;
use Shopsys\FrameworkBundle\Model\Product\Flag\FlagInlineEdit;

class FlagController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Flag\FlagFacade
     */
    protected $flagFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Flag\FlagInlineEdit
     */
    protected $flagInlineEdit;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\FlagFacade $flagFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\FlagInlineEdit $flagInlineEdit
     */
    public function __construct(
        FlagFacade $flagFacade,
        FlagInlineEdit $flagInlineEdit
    ) {
        $this->flagFacade = $flagFacade;
        $this->flagInlineEdit = $flagInlineEdit;
    }

    /**
     * @Route("/product/flag/list/")
     */
    public function listAction()
    {
        $productInlineEdit = $this->flagInlineEdit;

        $grid = $productInlineEdit->getGrid();

        return $this->render('@ShopsysFramework/Admin/Content/Flag/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }

    /**
     * @Route("/product/flag/delete/{id}", requirements={"id" = "\d+"})
     * @CsrfProtection
     * @param int $id
     */
    public function deleteAction($id)
    {
        try {
            $fullName = $this->flagFacade->getById($id)->getName();

            $this->flagFacade->deleteById($id);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Flag <strong>{{ name }}</strong> deleted'),
                [
                    'name' => $fullName,
                ]
            );
        } catch (\Shopsys\FrameworkBundle\Model\Product\Flag\Exception\FlagNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected flag doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_flag_list');
    }
}
