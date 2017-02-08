<?php

namespace Shopsys\GeneratorBundle\Controller;

use Shopsys\GeneratorBundle\Model\GeneratorFacade;
use Shopsys\GeneratorBundle\Model\GeneratorsFormFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GeneratorController extends Controller {

	public function indexAction(Request $request) {
		$generatorsFormFactory = $this->get(GeneratorsFormFactory::class);
		/* @var $generatorsFormFactory \Shopsys\GeneratorBundle\Model\GeneratorsFormFactory */
		$generatorFacade = $this->get(GeneratorFacade::class);
		/* @var $generatorFacade \Shopsys\GeneratorBundle\Model\GeneratorFacade */

		$form = $generatorsFormFactory->createForm();
		$form->handleRequest($request);

		$createdFiles = [];
		$errorMessage = null;

		if ($form->isValid()) {
			try {
				$createdFiles = $generatorFacade->generate($form->getData());
			} catch (\Shopsys\GeneratorBundle\Model\Exception\GeneratorTargetFileAlreadyExistsExpception $ex) {
				$errorMessage = $ex->getMessage();
			}
		}

		return $this->render('@ShopsysGenerator/index.html.twig', [
			'form' => $form->createView(),
			'generatorsNames' => $generatorFacade->getGeneratorsNames(),
			'createdFiles' => $createdFiles,
			'errorMessage' => $errorMessage,
		]);
	}

}
