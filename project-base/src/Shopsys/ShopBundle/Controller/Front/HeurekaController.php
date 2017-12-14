<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\ShopBundle\Component\Controller\FrontBaseController;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Heureka\HeurekaFacade;
use Shopsys\ShopBundle\Model\Heureka\HeurekaSetting;
use Symfony\Component\HttpFoundation\Response;

class HeurekaController extends FrontBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Model\Heureka\HeurekaFacade
     */
    private $heurekaFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Heureka\HeurekaSetting
     */
    private $heurekaSetting;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @param \Shopsys\ShopBundle\Model\Heureka\HeurekaFacade $heurekaFacade
     * @param \Shopsys\ShopBundle\Model\Heureka\HeurekaSetting $heurekaSetting
     * @param \Shopsys\ShopBundle\Component\Domain\Domain $domain
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
