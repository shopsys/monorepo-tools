<?php

namespace SS6\GeneratorBundle\Model;

use SS6\GeneratorBundle\Model\GeneratorCollection;
use SS6\GeneratorBundle\Model\GeneratorsFormFactory;
use Symfony\Component\HttpKernel\KernelInterface;

class GeneratorFacade {

	/**
	 * @var \SS6\GeneratorBundle\Model\GeneratorCollection
	 */
	private $generatorCollection;

	/**
	 * @var \Symfony\Component\HttpKernel\KernelInterface
	 */
	private $kernel;

	public function __construct(
		KernelInterface $kernel,
		GeneratorCollection $generatorCollection
	) {
		$this->kernel = $kernel;
		$this->generatorCollection = $generatorCollection;
	}

	/**
	 * @param array $formData
	 * @return string[]
	 */
	public function generate(array $formData) {
		$bundle = $this->kernel->getBundle($formData['bundle']);
		$filepaths = [];
		foreach ($this->generatorCollection->getGenerators() as $generator) {
			$generatorEnableFormName = $generator->getName() . GeneratorsFormFactory::GENERATOR_FORM_ENABLE_POSTFIX;
			if ($formData[$generatorEnableFormName] === true && array_key_exists($generator->getName(), $formData)) {
				$filepaths[] = $generator->generate($bundle, $formData[$generator->getName()]);
			}
		}

		return $filepaths;
	}

}
