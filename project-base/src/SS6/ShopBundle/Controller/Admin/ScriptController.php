<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Form\Admin\Script\ScriptFormType;
use SS6\ShopBundle\Model\Script\ScriptData;
use SS6\ShopBundle\Model\Script\ScriptFacade;
use Symfony\Component\HttpFoundation\Request;

class ScriptController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Script\ScriptFacade
	 */
	private $scriptFacade;

	public function __construct(ScriptFacade $scriptFacade) {
		$this->scriptFacade = $scriptFacade;
	}

	/**
	 * @Route("/script/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$form = $this->createForm(new ScriptFormType());
		$scriptData = new ScriptData();

		$form->setData($scriptData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$scriptData = $form->getData();

			$script = $this->transactional(
				function () use ($scriptData) {
					return $this->scriptFacade->create($scriptData);
				}
			);

			$this->getFlashMessageSender()
				->addSuccessFlashTwig(
					t('Byl vytvořen skript <strong>{{ name }}</strong>'),
					[
						'name' => $script->getName(),
					]
				);
			return $this->render('@SS6Shop/Admin/Content/Script/new.html.twig', [
				'form' => $form->createView(),
			]);
		}

		return $this->render('@SS6Shop/Admin/Content/Script/new.html.twig', [
			'form' => $form->createView(),
		]);
	}
}
