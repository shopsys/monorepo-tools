<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Component\Controller\FrontBaseController;
use SS6\ShopBundle\Model\Script\ScriptFacade;

class ScriptController extends FrontBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Script\ScriptFacade
	 */
	private $scriptFacade;

	public function __construct(ScriptFacade $scriptFacade) {
		$this->scriptFacade = $scriptFacade;
	}

	/**
	 * @param string $placement
	 */
	public function embedAction($placement) {
		return $this->render('@SS6Shop/Front/Inline/MeasuringScript/scripts.html.twig', [
			'scripts' => $this->scriptFacade->getScriptsByPlacement($placement),
		]);
	}

}
