<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory;
use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\FrameworkBundle\Form\Admin\Pricing\Group\PricingGroupSettingsFormType;
use Shopsys\FrameworkBundle\Model\Pricing\Group\Grid\PricingGroupInlineEdit;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PricingGroupController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade
     */
    private $pricingGroupSettingFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade
     */
    private $pricingGroupFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\Grid\PricingGroupInlineEdit
     */
    private $pricingGroupInlineEdit;

    /**
     * @var \Shopsys\FrameworkBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory
     */
    private $confirmDeleteResponseFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    public function __construct(
        PricingGroupSettingFacade $pricingGroupSettingFacade,
        PricingGroupFacade $pricingGroupFacade,
        PricingGroupInlineEdit $pricingGroupInlineEdit,
        ConfirmDeleteResponseFactory $confirmDeleteResponseFactory,
        AdminDomainTabsFacade $adminDomainTabsFacade
    ) {
        $this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
        $this->pricingGroupFacade = $pricingGroupFacade;
        $this->pricingGroupInlineEdit = $pricingGroupInlineEdit;
        $this->confirmDeleteResponseFactory = $confirmDeleteResponseFactory;
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
    }

    /**
     * @Route("/pricing/group/list/")
     */
    public function listAction()
    {
        $grid = $this->pricingGroupInlineEdit->getGrid();

        return $this->render('@ShopsysFramework/Admin/Content/Pricing/Groups/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }

    /**
     * @Route("/pricing/group/delete/{id}", requirements={"id" = "\d+"})
     * @CsrfProtection
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     */
    public function deleteAction(Request $request, $id)
    {
        $newId = $request->get('newId');
        $newId = $newId !== null ? (int)$newId : null;

        try {
            $name = $this->pricingGroupFacade->getById($id)->getName();

            $this->pricingGroupFacade->delete($id, $newId);

            if ($newId === null) {
                $this->getFlashMessageSender()->addSuccessFlashTwig(
                    t('Pricing group <strong>{{ name }}</strong> deleted'),
                    [
                        'name' => $name,
                    ]
                );
            } else {
                $newPricingGroup = $this->pricingGroupFacade->getById($newId);
                $this->getFlashMessageSender()->addSuccessFlashTwig(
                    t('Pricing group <strong>{{ name }}</strong> deleted and replaced by group <strong>{{ newName }}</strong>.'),
                    [
                        'name' => $name,
                        'newName' => $newPricingGroup->getName(),
                    ]
                );
            }
        } catch (\Shopsys\FrameworkBundle\Model\Pricing\Group\Exception\PricingGroupNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected pricing group doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_pricinggroup_list');
    }

    /**
     * @Route("/pricing/group/delete-confirm/{id}", requirements={"id" = "\d+"})
     * @param int $id
     */
    public function deleteConfirmAction($id)
    {
        try {
            $pricingGroup = $this->pricingGroupFacade->getById($id);

            if ($this->pricingGroupSettingFacade->isPricingGroupUsedOnSelectedDomain($pricingGroup)) {
                $message = t(
                    'For removing pricing group "%name%" you have to choose other one to be set everywhere where the existing one is used. '
                    . 'Which pricing group you want to set instead?',
                    ['%name%' => $pricingGroup->getName()]
                );

                if ($this->pricingGroupSettingFacade->isPricingGroupDefaultOnSelectedDomain($pricingGroup)) {
                    $message = t(
                        'Pricing group "%name%" set as default. For deleting it you have to choose other one to be set everywhere '
                        . 'where the existing one is used. Which pricing group you want to set instead?',
                        ['%name%' => $pricingGroup->getName()]
                    );
                }

                return $this->confirmDeleteResponseFactory->createSetNewAndDeleteResponse(
                    $message,
                    'admin_pricinggroup_delete',
                    $id,
                    $this->pricingGroupFacade->getAllExceptIdByDomainId($id, $pricingGroup->getDomainId())
                );
            } else {
                $message = t(
                    'Do you really want to remove pricing group "%name%" permanently? It is not used anywhere.',
                    ['%name%' => $pricingGroup->getName()]
                );
                return $this->confirmDeleteResponseFactory->createDeleteResponse($message, 'admin_pricinggroup_delete', $id);
            }
        } catch (\Shopsys\FrameworkBundle\Model\Pricing\Group\Exception\PricingGroupNotFoundException $ex) {
            return new Response(t('Selected pricing group doesn\'t exist.'));
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function settingsAction(Request $request)
    {
        $domainId = $this->adminDomainTabsFacade->getSelectedDomainId();
        $pricingGroupSettingsFormData = [
            'defaultPricingGroup' => $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainId),
        ];

        $form = $this->createForm(PricingGroupSettingsFormType::class, $pricingGroupSettingsFormData, [
            'domain_id' => $domainId,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pricingGroupSettingsFormData = $form->getData();

            $this->pricingGroupSettingFacade->setDefaultPricingGroupForSelectedDomain($pricingGroupSettingsFormData['defaultPricingGroup']);

            $this->getFlashMessageSender()->addSuccessFlash(t('Default pricing group settings modified'));

            return $this->redirectToRoute('admin_pricinggroup_list');
        }

        return $this->render('@ShopsysFramework/Admin/Content/Pricing/Groups/pricingGroupSettings.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
