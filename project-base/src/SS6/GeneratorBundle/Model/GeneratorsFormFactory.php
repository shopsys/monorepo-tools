<?php

namespace SS6\GeneratorBundle\Model;

use SS6\GeneratorBundle\Model\GeneratorCollection;
use SS6\ShopBundle\Form\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class GeneratorsFormFactory {

	const GENERATOR_FORM_ENABLE_POSTFIX = '_enable';

	/**
	 * @var \SS6\GeneratorBundle\Model\GeneratorCollection
	 */
	private $generatorCollection;

	/**
	 * @var \Symfony\Component\Form\FormFactoryInterface
	 */
	private $formFactory;

	/**
	 * @var \Symfony\Component\HttpKernel\KernelInterface
	 */
	private $kernel;

	public function __construct(
		KernelInterface $kernel,
		GeneratorCollection $generatorCollection,
		FormFactoryInterface $formFactory
	) {
		$this->kernel = $kernel;
		$this->generatorCollection = $generatorCollection;
		$this->formFactory = $formFactory;
	}

	/**
	 * $return \Symfony\Component\Form\Form
	 */
	public function createForm() {
		$formBuilder = $this->formFactory->createNamedBuilder(
			'formName',
			FormType::FORM,
			null,
			[
				'attr' => ['novalidate' => 'novalidate'],
			]
		);

		foreach ($this->generatorCollection->getGenerators() as $generator) {
			$generatorFormBuilder = $this->formFactory->createNamedBuilder($generator->getName());
			$generator->buildForm($generatorFormBuilder);
			$formBuilder
				->add($generator->getName() . self::GENERATOR_FORM_ENABLE_POSTFIX, FormType::CHECKBOX)
				->add($generatorFormBuilder);
		}

		$formBuilder->add('bundle', FormType::CHOICE, [
			'choices' => $this->getBundleChoices(),
			'data' => 'SS6ShopBundle',
		]);
		$formBuilder->add('submit', FormType::SUBMIT);

		$form = $formBuilder->getForm();

		return $form;
	}

	/**
	 * @return string[]
	 */
	private function getBundleChoices() {
		$bundleChoices = [];
		foreach ($this->kernel->getBundles() as $bundle) {
			if (substr($bundle->getNamespace(), 0, 3) === 'SS6') {
				$bundleChoices[$bundle->getName()] = $bundle->getName();
			}
		}

		return $bundleChoices;
	}

}
