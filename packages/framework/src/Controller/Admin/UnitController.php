<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory;
use Shopsys\FrameworkBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\FrameworkBundle\Form\Admin\Product\Unit\UnitSettingFormType;
use Shopsys\FrameworkBundle\Model\Product\Unit\UnitFacade;
use Shopsys\FrameworkBundle\Model\Product\Unit\UnitInlineEdit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UnitController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Unit\UnitFacade
     */
    private $unitFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Unit\UnitInlineEdit
     */
    private $unitInlineEdit;

    /**
     * @var \Shopsys\FrameworkBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory
     */
    private $confirmDeleteResponseFactory;

    public function __construct(
        UnitFacade $unitFacade,
        UnitInlineEdit $unitInlineEdit,
        ConfirmDeleteResponseFactory $confirmDeleteResponseFactory
    ) {
        $this->unitFacade = $unitFacade;
        $this->unitInlineEdit = $unitInlineEdit;
        $this->confirmDeleteResponseFactory = $confirmDeleteResponseFactory;
    }

    /**
     * @Route("/product/unit/list/")
     */
    public function listAction()
    {
        $unitInlineEdit = $this->unitInlineEdit;

        $grid = $unitInlineEdit->getGrid();

        return $this->render('@ShopsysFramework/Admin/Content/Unit/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }

    /**
     * @Route("/unit/delete-confirm/{id}", requirements={"id" = "\d+"})
     * @param int $id
     */
    public function deleteConfirmAction($id)
    {
        try {
            $unit = $this->unitFacade->getById($id);
            $isUnitDefault = $this->unitFacade->isUnitDefault($unit);

            if ($this->unitFacade->isUnitUsed($unit) || $isUnitDefault) {
                if ($isUnitDefault) {
                    $message = t(
                        'Unit "%name%" set as default. For deleting existing unit you have to choose new default unit. '
                        . 'Which unit you want to set instead?',
                        ['%name%' => $unit->getName()]
                    );
                } else {
                    $message = t(
                        'For deleting unit "%name%" you have to choose other one to be set everywhere where the existing one is used. '
                        . 'Which unit you want to set instead?',
                        ['%name%' => $unit->getName()]
                    );
                }

                return $this->confirmDeleteResponseFactory->createSetNewAndDeleteResponse(
                    $message,
                    'admin_unit_delete',
                    $id,
                    $this->unitFacade->getAllExceptId($id)
                );
            } else {
                $message = t(
                    'Do you really want to remove unit "%name%" permanently? It is not used anywhere.',
                    ['%name%' => $unit->getName()]
                );

                return $this->confirmDeleteResponseFactory->createDeleteResponse($message, 'admin_unit_delete', $id);
            }
        } catch (\Shopsys\FrameworkBundle\Model\Product\Unit\Exception\UnitNotFoundException $ex) {
            return new Response(t('Selected unit doesn\'t exist'));
        }
    }

    /**
     * @Route("/product/unit/delete/{id}", requirements={"id" = "\d+"})
     * @CsrfProtection
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     */
    public function deleteAction(Request $request, $id)
    {
        $newId = $request->get('newId');

        try {
            $fullName = $this->unitFacade->getById($id)->getName();

            $this->unitFacade->deleteById($id, $newId);

            if ($newId === null) {
                $this->getFlashMessageSender()->addSuccessFlashTwig(
                    t('Unit <strong>{{ name }}</strong> deleted'),
                    [
                        'name' => $fullName,
                    ]
                );
            } else {
                $newUnit = $this->unitFacade->getById($newId);
                $this->getFlashMessageSender()->addSuccessFlashTwig(
                    t('Unit <strong>{{ name }}</strong> deleted and replaced by unit <strong>{{ newName }}</strong>'),
                    [
                        'name' => $fullName,
                        'newName' => $newUnit->getName(),
                    ]
                );
            }
        } catch (\Shopsys\FrameworkBundle\Model\Product\Unit\Exception\UnitNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected unit doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_unit_list');
    }

    /**
     * @Route("/product/unit/setting/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function settingAction(Request $request)
    {
        $unitSettingsFormData = ['defaultUnit' => $this->unitFacade->getDefaultUnit()];

        $form = $this->createForm(UnitSettingFormType::class, $unitSettingsFormData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $unitSettingsFormData = $form->getData();

            $this->unitFacade->setDefaultUnit($unitSettingsFormData['defaultUnit']);

            $this->getFlashMessageSender()->addSuccessFlash(t('Default unit settings modified'));

            return $this->redirectToRoute('admin_unit_list');
        }

        return $this->render('@ShopsysFramework/Admin/Content/Unit/setting.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
