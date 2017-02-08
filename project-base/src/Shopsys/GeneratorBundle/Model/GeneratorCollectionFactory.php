<?php

namespace SS6\GeneratorBundle\Model;

use SS6\GeneratorBundle\Model\Generator\RepositoryGenerator;
use SS6\GeneratorBundle\Model\GeneratorCollection;

class GeneratorCollectionFactory {

	/**
	 * @var \SS6\GeneratorBundle\Model\Generator\RepositoryGenerator
	 */
	private $repositoryGenerator;

	/**
	 * @var string[]
	 */
	private $skeletonDirs;

	public function __construct(array $skeletonDirs, RepositoryGenerator $repositoryGenerator) {
		$this->skeletonDirs = $skeletonDirs;
		$this->repositoryGenerator = $repositoryGenerator;
	}

	/**
	 * @return \SS6\GeneratorBundle\Model\GeneratorCollection
	 */
	public function create() {
		$generatorCollection = new GeneratorCollection($this->skeletonDirs);
		$generatorCollection->addGenerator($this->repositoryGenerator);

		return $generatorCollection;
	}

}
