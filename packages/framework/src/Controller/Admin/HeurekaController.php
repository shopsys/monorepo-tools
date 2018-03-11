<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Controller\AdminBaseController;
use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Form\Admin\Heureka\HeurekaShopCertificationFormType;
use Shopsys\FrameworkBundle\Model\Heureka\HeurekaFacade;
use Shopsys\FrameworkBundle\Model\Heureka\HeurekaSetting;
use Symfony\Component\HttpFoundation\Request;

class HeurekaController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Heureka\HeurekaSetting
     */
    private $heurekaSetting;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Heureka\HeurekaFacade
     */
    private $heurekaFacade;

    public function __construct(AdminDomainTabsFacade $adminDomainTabsFacade, HeurekaSetting $heurekaSetting, HeurekaFacade $heurekaFacade)
    {
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
        $this->heurekaSetting = $heurekaSetting;
        $this->heurekaFacade = $heurekaFacade;
    }

    /**
     * @Route("/heureka/setting/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function settingAction(Request $request)
    {
        $domainId = $this->adminDomainTabsFacade->getSelectedDomainId();
        $domainConfig = $this->adminDomainTabsFacade->getSelectedDomainConfig();
        $locale = $domainConfig->getLocale();
        $formView = null;
        $serverName = $this->heurekaFacade->getServerNameByLocale($locale);

        if ($this->heurekaFacade->isDomainLocaleSupported($locale)) {
            $heurekaShopCertificationData = [
                'heurekaApiKey' => $this->heurekaSetting->getApiKeyByDomainId($domainId),
                'heurekaWidgetCode' => $this->heurekaSetting->getHeurekaShopCertificationWidgetByDomainId($domainId),
            ];

            $form = $this->createForm(HeurekaShopCertificationFormType::class, $heurekaShopCertificationData, [
                'server_name' => $serverName,
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $heurekaShopCertificationData = $form->getData();

                $this->heurekaSetting->setApiKeyForDomain($heurekaShopCertificationData['heurekaApiKey'], $domainId);
                $this->heurekaSetting->setHeurekaShopCertificationWidgetForDomain(
                    $heurekaShopCertificationData['heurekaWidgetCode'],
                    $domainId
                );

                $this->getFlashMessageSender()->addSuccessFlash(t('Settings modified.'));
            }
            $formView = $form->createView();
        }

        return $this->render('@ShopsysFramework/Admin/Content/Heureka/setting.html.twig', [
            'form' => $formView,
            'serverName' => $serverName,
            'selectedDomainConfig' => $domainConfig,
        ]);
    }
}
