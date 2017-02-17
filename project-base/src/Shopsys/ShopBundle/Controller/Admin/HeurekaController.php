<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Form\Admin\Heureka\HeurekaShopCertificationFormType;
use Shopsys\ShopBundle\Model\Heureka\HeurekaFacade;
use Shopsys\ShopBundle\Model\Heureka\HeurekaSetting;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HeurekaController extends AdminBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
     */
    private $selectedDomain;

    /**
     * @var \Shopsys\ShopBundle\Model\Heureka\HeurekaSetting
     */
    private $heurekaSetting;

    /**
     * @var \Shopsys\ShopBundle\Model\Heureka\HeurekaFacade
     */
    private $heurekaFacade;

    public function __construct(SelectedDomain $selectedDomain, HeurekaSetting $heurekaSetting, HeurekaFacade $heurekaFacade)
    {
        $this->selectedDomain = $selectedDomain;
        $this->heurekaSetting = $heurekaSetting;
        $this->heurekaFacade = $heurekaFacade;
    }

    /**
     * @Route("/heureka/setting/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function settingAction(Request $request)
    {
        $domainId = $this->selectedDomain->getId();
        $domainConfig = $this->selectedDomain->getCurrentSelectedDomain();
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
        $domainId = $this->selectedDomain->getId();

        if (!$this->heurekaFacade->isHeurekaWidgetActivated($domainId)) {
            return new Response('');
        }

        return $this->render('@ShopsysShop/Admin/Content/Heureka/widget.html.twig', [
            'widgetCode' => $this->heurekaSetting->getHeurekaShopCertificationWidgetByDomainId($domainId),
        ]);
    }
}
