<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\ShopBundle\Form\Admin\Vat\VatSettingsFormType;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatInlineEdit;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VatController extends AdminBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory
     */
    private $confirmDeleteResponseFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\PricingSetting
     */
    private $pricingSetting;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade
     */
    private $vatFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Vat\VatInlineEdit
     */
    private $vatInlineEdit;

    public function __construct(
        VatFacade $vatFacade,
        PricingSetting $pricingSetting,
        VatInlineEdit $vatInlineEdit,
        ConfirmDeleteResponseFactory $confirmDeleteResponseFactory
    ) {
        $this->vatFacade = $vatFacade;
        $this->pricingSetting = $pricingSetting;
        $this->vatInlineEdit = $vatInlineEdit;
        $this->confirmDeleteResponseFactory = $confirmDeleteResponseFactory;
    }

    /**
     * @Route("/vat/list/")
     */
    public function listAction()
    {
        $grid = $this->vatInlineEdit->getGrid();

        return $this->render('@ShopsysShop/Admin/Content/Vat/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }

    /**
     * @Route("/vat/delete-confirm/{id}", requirements={"id" = "\d+"})
     * @param int $id
     */
    public function deleteConfirmAction($id)
    {
        try {
            $vat = $this->vatFacade->getById($id);
            if ($this->vatFacade->isVatUsed($vat)) {
                $message = t(
                    'For deleting rate  "%name%" you have to choose other one to be set everywhere where the existing one is used. '
                    . 'After changing the VAT rate products prices will be recalculated - base price with VAT will remain same. '
                    . 'Which unit you want to set instead?',
                    ['%name%' => $vat->getName()]
                );
                $remainingVatsList = new ObjectChoiceList($this->vatFacade->getAllExceptId($id), 'name', [], null, 'id');

                return $this->confirmDeleteResponseFactory->createSetNewAndDeleteResponse(
                    $message,
                    'admin_vat_delete',
                    $id,
                    $remainingVatsList
                );
            } else {
                $message = t(
                    'Do you really want to remove rate "%name%" permanently? It is not used anywhere.',
                    ['%name%' => $vat->getName()]
                );

                return $this->confirmDeleteResponseFactory->createDeleteResponse($message, 'admin_vat_delete', $id);
            }
        } catch (\Shopsys\ShopBundle\Model\Pricing\Vat\Exception\VatNotFoundException $ex) {
            return new Response(t('Selected VAT doesn\'t exist'));
        }
    }

    /**
     * @Route("/vat/delete/{id}", requirements={"id" = "\d+"})
     * @CsrfProtection
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     */
    public function deleteAction(Request $request, $id)
    {
        $newId = $request->get('newId');

        try {
            $fullName = $this->vatFacade->getById($id)->getName();

            $this->vatFacade->deleteById($id, $newId);

            if ($newId === null) {
                $this->getFlashMessageSender()->addSuccessFlashTwig(
                    t('VAT <strong>{{ name }}</strong> deleted'),
                    [
                        'name' => $fullName,
                    ]
                );
            } else {
                $newVat = $this->vatFacade->getById($newId);
                $this->getFlashMessageSender()->addSuccessFlashTwig(
                    t('VAT <strong>{{ name }}</strong> deleted and replaced by <strong>{{ newName }}</strong>.'),
                    [
                        'name' => $fullName,
                        'newName' => $newVat->getName(),
                    ]
                );
            }
        } catch (\Shopsys\ShopBundle\Model\Pricing\Vat\Exception\VatNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected VAT doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_vat_list');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function settingsAction(Request $request)
    {
        $vats = $this->vatFacade->getAll();
        $form = $this->createForm(new VatSettingsFormType(
            $vats,
            PricingSetting::getRoundingTypes()
        ));

            $vatSettingsFormData = [];
            $vatSettingsFormData['defaultVat'] = $this->vatFacade->getDefaultVat();
            $vatSettingsFormData['roundingType'] = $this->pricingSetting->getRoundingType();

            $form->setData($vatSettingsFormData);
            $form->handleRequest($request);

        if ($form->isValid()) {
            $vatSettingsFormData = $form->getData();

            try {
                $this->vatFacade->setDefaultVat($vatSettingsFormData['defaultVat']);
                $this->pricingSetting->setRoundingType($vatSettingsFormData['roundingType']);

                $this->getFlashMessageSender()->addSuccessFlash(t('VAT settings modified'));

                return $this->redirectToRoute('admin_vat_list');
            } catch (\Shopsys\ShopBundle\Model\Pricing\Exception\InvalidRoundingTypeException $ex) {
                $this->getFlashMessageSender()->addErrorFlash(t('Invalid rounding settings'));
            }
        }

        return $this->render('@ShopsysShop/Admin/Content/Vat/vatSettings.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
