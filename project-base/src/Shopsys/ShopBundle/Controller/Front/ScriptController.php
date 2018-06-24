<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Script\ScriptFacade;
use Symfony\Component\HttpFoundation\Response;

class ScriptController extends FrontBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Script\ScriptFacade
     */
    private $scriptFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(
        ScriptFacade $scriptFacade,
        Domain $domain
    ) {
        $this->scriptFacade = $scriptFacade;
        $this->domain = $domain;
    }

    public function embedAllPagesScriptsAction()
    {
        return $this->render('@ShopsysShop/Front/Inline/MeasuringScript/scripts.html.twig', [
            'scriptsCodes' => $this->scriptFacade->getAllPagesScriptCodes(),
        ]);
    }

    public function embedAllPagesGoogleAnalyticsScriptAction()
    {
        if (!$this->scriptFacade->isGoogleAnalyticsActivated($this->domain->getId())) {
            return new Response('');
        }

        return $this->render('@ShopsysShop/Front/Inline/MeasuringScript/googleAnalytics.html.twig', [
            'trackingId' => $this->scriptFacade->getGoogleAnalyticsTrackingId($this->domain->getId()),
        ]);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     */
    public function embedOrderSentPageScriptsAction(Order $order)
    {
        return $this->render('@ShopsysShop/Front/Inline/MeasuringScript/scripts.html.twig', [
            'scriptsCodes' => $this->scriptFacade->getOrderSentPageScriptCodesWithReplacedVariables($order),
        ]);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     */
    public function embedOrderSentPageGoogleAnalyticsScriptAction(Order $order)
    {
        if (!$this->scriptFacade->isGoogleAnalyticsActivated($this->domain->getId())) {
            return new Response('');
        }

        return $this->render('@ShopsysShop/Front/Inline/MeasuringScript/googleAnalyticsEcommerce.html.twig', [
            'order' => $order,
        ]);
    }
}
