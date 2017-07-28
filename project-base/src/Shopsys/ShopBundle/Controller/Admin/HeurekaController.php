<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\ShopBundle\Form\Admin\Heureka\HeurekaShopCertificationFormType;
use Shopsys\ShopBundle\Model\Heureka\HeurekaFacade;
use Shopsys\ShopBundle\Model\Heureka\HeurekaSetting;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HeurekaController extends AdminBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Heureka\HeurekaSetting
     */
    private $heurekaSetting;

    /**
     * @var \Shopsys\ShopBundle\Model\Heureka\HeurekaFacade
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

        if ($this->heurekaFacade->isDomainLocaleSupported($locale)) {
            $heurekaShopCertificationData = [
                'heurekaApiKey' => $this->heurekaSetting->getApiKeyByDomainId($domainId),
                'heurekaWidgetCode' => $this->heurekaSetting->getHeurekaShopCertificationWidgetByDomainId($domainId),
            ];

            $form = $this->createForm(HeurekaShopCertificationFormType::class, $heurekaShopCertificationData);
            $form->handleRequest($request);

            if ($form->isValid()) {
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

        return $this->render('@ShopsysShop/Admin/Content/Heureka/setting.html.twig', [
            'form' => $formView,
            'serverName' => $this->heurekaFacade->getServerNameByLocale($locale),
            'selectedDomainConfig' => $domainConfig,
        ]);
    }

    public function embedWidgetAction()
    {
        $domainId = $this->adminDomainTabsFacade->getSelectedDomainId();

        if (!$this->heurekaFacade->isHeurekaWidgetActivated($domainId)) {
            return new Response('');
        }

        return $this->render('@ShopsysShop/Admin/Content/Heureka/widget.html.twig', [
            'widgetCode' => $this->heurekaSetting->getHeurekaShopCertificationWidgetByDomainId($domainId),
        ]);
    }
}
