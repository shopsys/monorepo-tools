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

	public function embedAction() {
		$scripts = $this->scriptFacade->getAll();

		return $this->render('@SS6Shop/Front/Inline/MeasuringScript/scripts.html.twig', [
			'scripts' => $scripts,
		]);
	}
}
