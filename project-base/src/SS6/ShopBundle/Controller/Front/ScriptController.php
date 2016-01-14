<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Component\Controller\FrontBaseController;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Script\ScriptFacade;

class ScriptController extends FrontBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Script\ScriptFacade
	 */
	private $scriptFacade;

	public function __construct(ScriptFacade $scriptFacade) {
		$this->scriptFacade = $scriptFacade;
	}

	public function embedAllPagesScriptsAction() {
		return $this->render('@SS6Shop/Front/Inline/MeasuringScript/scripts.html.twig', [
			'scriptsCodes' => $this->scriptFacade->getAllPagesScriptCodes(),
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

}
