<?php

namespace SS6\GeneratorBundle\Model\Generator;

use SS6\GeneratorBundle\Model\AbstractGenerator;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class RepositoryGenerator extends AbstractGenerator {

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder) {
		$builder
			->add('entityNamespace', 'text')
			->add('entityName', 'text');
	}

	/**
	 * {@inheritdoc}
	 */
	public function generate(BundleInterface $bundle, array $formData) {
		$entityNamespace = $formData['entityNamespace'];
		$entityName = $formData['entityName'];
		$targetFilepath = $bundle->getPath() . '/Model/' . $entityNamespace . '/' . $entityName . 'Repository.php';
		$this->renderFile('Repository.php.twig', $targetFilepath, [
			'entityName' => $entityName,
			'namespace' => $bundle->getNamespace() . '\Model\\' . $entityNamespace,
		]);

		return $targetFilepath;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'repository';
	}

}
