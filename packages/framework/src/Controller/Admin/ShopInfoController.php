<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Form\Admin\ShopInfo\ShopInfoSettingFormType;
use Shopsys\FrameworkBundle\Model\ShopInfo\ShopInfoSettingFacade;
use Symfony\Component\HttpFoundation\Request;

class ShopInfoController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\ShopInfo\ShopInfoSettingFacade
     */
    private $shopInfoSettingFacade;

    public function __construct(
        ShopInfoSettingFacade $shopInfoSettingFacade,
        AdminDomainTabsFacade $adminDomainTabsFacade
    ) {
        $this->shopInfoSettingFacade = $shopInfoSettingFacade;
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
    }

    /**
     * @Route("/shop-info/setting/")
     */
    public function settingAction(Request $request)
    {
        $selectedDomainId = $this->adminDomainTabsFacade->getSelectedDomainId();

        $shopInfoSettingData = [
            'phoneNumber' => $this->shopInfoSettingFacade->getPhoneNumber($selectedDomainId),
            'email' => $this->shopInfoSettingFacade->getEmail($selectedDomainId),
            'phoneHours' => $this->shopInfoSettingFacade->getPhoneHours($selectedDomainId),
        ];

        $form = $this->createForm(ShopInfoSettingFormType::class, $shopInfoSettingData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $shopInfoSettingData = $form->getData();

            $this->shopInfoSettingFacade->setPhoneNumber($shopInfoSettingData['phoneNumber'], $selectedDomainId);
            $this->shopInfoSettingFacade->setEmail($shopInfoSettingData['email'], $selectedDomainId);
            $this->shopInfoSettingFacade->setPhoneHours($shopInfoSettingData['phoneHours'], $selectedDomainId);

            $this->getFlashMessageSender()->addSuccessFlash(t('E-shop attributes settings modified'));

            return $this->redirectToRoute('admin_shopinfo_setting');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        return $this->render('@ShopsysFramework/Admin/Content/ShopInfo/shopInfo.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
