<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Heureka\HeurekaFacade;
use Shopsys\FrameworkBundle\Model\Heureka\HeurekaSetting;
use Symfony\Component\HttpFoundation\Response;

class HeurekaController extends FrontBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Heureka\HeurekaFacade
     */
    private $heurekaFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Heureka\HeurekaSetting
     */
    private $heurekaSetting;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Heureka\HeurekaFacade $heurekaFacade
     * @param \Shopsys\FrameworkBundle\Model\Heureka\HeurekaSetting $heurekaSetting
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(HeurekaFacade $heurekaFacade, HeurekaSetting $heurekaSetting, Domain $domain)
    {
        $this->heurekaFacade = $heurekaFacade;
        $this->heurekaSetting = $heurekaSetting;
        $this->domain = $domain;
    }

    public function embedWidgetAction()
    {
        $domainId = $this->domain->getId();

        if (!$this->heurekaFacade->isHeurekaWidgetActivated($domainId)) {
            return new Response('');
        }

        return $this->render('@ShopsysShop/Front/Content/Heureka/widget.html.twig', [
            'widgetCode' => $this->heurekaSetting->getHeurekaShopCertificationWidgetByDomainId($domainId),
        ]);
    }
}
