<?php

namespace Shopsys\GeneratorBundle\Model;

use Shopsys\GeneratorBundle\Model\GeneratorCollectionFactory;
use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Form\ValidationGroup;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class GeneratorsFormFactory {

	const GENERATOR_FORM_ENABLE_POSTFIX = '_enable';

	/**
	 * @var \Shopsys\GeneratorBundle\Model\GeneratorCollection
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
		GeneratorCollectionFactory $generatorCollectionFactory,
		FormFactoryInterface $formFactory
	) {
		$this->kernel = $kernel;
		$this->generatorCollection = $generatorCollectionFactory->create();
		$this->formFactory = $formFactory;
	}

	/**
	 * $return \Symfony\Component\Form\Form
	 */
	public function createForm() {
		$formBuilder = $this->formFactory->createNamedBuilder(
			'generators',
			FormType::FORM,
			null,
			[
				'attr' => ['novalidate' => 'novalidate'],
			]
		);

		foreach ($this->generatorCollection->getGenerators() as $generator) {
			$generatorFormBuilder = $this->formFactory->createNamedBuilder(
				$generator->getName(),
				FormType::FORM,
				null,
				[
					'validation_groups' => function (FormInterface $form) use ($generator) {
						$generatorEnabled = $form->getParent()->getData()[$generator->getName() . self::GENERATOR_FORM_ENABLE_POSTFIX];
						if ($generatorEnabled) {
							return [ValidationGroup::VALIDATION_GROUP_DEFAULT];
						} else {
							return [];
						}
					},
				]
			);
			$generator->buildForm($generatorFormBuilder);
			$formBuilder
				->add($generator->getName() . self::GENERATOR_FORM_ENABLE_POSTFIX, FormType::CHECKBOX)
				->add($generatorFormBuilder);
		}

		$formBuilder->add('bundle', FormType::CHOICE, [
			'choices' => $this->getBundleChoices(),
			'data' => 'ShopsysShopBundle',
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
