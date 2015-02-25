<?php

namespace SS6\GeneratorBundle\Controller;

use SS6\GeneratorBundle\Model\GeneratorFacade;
use SS6\GeneratorBundle\Model\GeneratorsFormFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GeneratorController extends Controller {

	public function indexAction(Request $request) {
		$generatorsFormFactory = $this->get(GeneratorsFormFactory::class);
		/* @var $generatorsFormFactory \SS6\GeneratorBundle\Model\GeneratorsFormFactory */
		$generatorFacade = $this->get(GeneratorFacade::class);
		/* @var $generatorFacade \SS6\GeneratorBundle\Model\GeneratorFacade */

		$form = $generatorsFormFactory->createForm();
		$form->handleRequest($request);

		$createdFiles = [];
		$errorMessage = null;

		if ($form->isValid()) {
			try {
				$createdFiles = $generatorFacade->generate($form->getData());
			} catch (\SS6\GeneratorBundle\Model\Exception\GeneratorTargetFileAlreadyExistsExpception $ex) {
				$errorMessage = $ex->getMessage();
			}
		}

		return $this->render('@SS6Generator/index.html.twig', [
			'form' => $form->createView(),
			'generatorsNames' => $generatorFacade->getGeneratorsNames(),
			'createdFiles' => $createdFiles,
			'errorMessage' => $errorMessage,
		]);
	}

}
