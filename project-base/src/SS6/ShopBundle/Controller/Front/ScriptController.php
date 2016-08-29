<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Component\Controller\FrontBaseController;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Script\ScriptFacade;
use Symfony\Component\HttpFoundation\Response;

class ScriptController extends FrontBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Script\ScriptFacade
	 */
	private $scriptFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	public function __construct(
		ScriptFacade $scriptFacade,
		Domain $domain
	) {
		$this->scriptFacade = $scriptFacade;
		$this->domain = $domain;
	}

	public function embedAllPagesScriptsAction() {
		return $this->render('@SS6Shop/Front/Inline/MeasuringScript/scripts.html.twig', [
			'scriptsCodes' => $this->scriptFacade->getAllPagesScriptCodes(),
		]);
	}

	public function embedAllPagesGoogleAnalyticsScriptAction() {
		if (!$this->scriptFacade->isGoogleAnalyticsActivated($this->domain->getId())) {
			return new Response('');
		}

		return $this->render('@SS6Shop/Front/Inline/MeasuringScript/googleAnalytics.html.twig', [
			'trackingId' => $this->scriptFacade->getGoogleAnalyticsTrackingId($this->domain->getId()),
		]);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 */
	public function embedOrderSentPageScriptsAction(Order $order) {
		return $this->render('@SS6Shop/Front/Inline/MeasuringScript/scripts.html.twig', [
			'scriptsCodes' => $this->scriptFacade->getOrderSentPageScriptCodesWithReplacedVariables($order),
		]);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 */
	public function embedOrderSentPageGoogleAnalyticsScriptAction(Order $order) {
		if (!$this->scriptFacade->isGoogleAnalyticsActivated($this->domain->getId())) {
			return new Response('');
		}

		return $this->render('@SS6Shop/Front/Inline/MeasuringScript/googleAnalyticsEcommerce.html.twig', [
			'order' => $order,
		]);
	}

}
