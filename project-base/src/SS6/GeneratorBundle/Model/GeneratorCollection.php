<?php

namespace SS6\GeneratorBundle\Model;

use SS6\GeneratorBundle\Model\AbstractGenerator;
use SS6\GeneratorBundle\Model\GeneratorInterface;
use Twig_Environment;
use Twig_Loader_Filesystem;

class GeneratorCollection {

	/**
	 * @var \SS6\GeneratorBundle\Model\GeneratorInterface[]
	 */
	private $generatorsByName;

	/**
	 * @var \Twig_Environment|null
	 */
	private $twig;

	/**
	 * @var string[]
	 */
	private $skeletonDirs;

	/**
	 * @param string[] $skeletonDirs
	 */
	public function __construct(array $skeletonDirs) {
		$this->generatorsByName = [];
		$this->skeletonDirs = $skeletonDirs;
	}

	/**
	 * @param \SS6\GeneratorBundle\Model\GeneratorInterface $generator
	 */
	public function addGenerator(GeneratorInterface $generator) {
		if ($this->has($generator->getName())) {
			throw new \SS6\GeneratorBundle\Model\Exception\DuplicateGeneratorNameException($generator->getName());
		}
		if ($generator instanceof AbstractGenerator) {
			$generator->setTwig($this->getTwig());
		}
		$this->generatorsByName[$generator->getName()] = $generator;
	}

	/**
	 * @param string $generatorName
	 * @return bool
	 */
	public function has($generatorName) {
		return array_key_exists($generatorName, $this->generatorsByName);
	}

	/**
	 * @return \SS6\GeneratorBundle\Model\GeneratorInterface[]
	 */
	public function getGenerators() {
		return $this->generatorsByName;
	}

	/**
	 * @return \Twig_Environment
	 */
	private function getTwig() {
		if ($this->twig === null) {
			$this->twig = new Twig_Environment(
				new Twig_Loader_Filesystem($this->skeletonDirs),
				[
					'debug' => true,
					'cache' => false,
					'strict_variables' => true,
					'autoescape' => false,
				]
			);
		}

		return $this->twig;
	}
}
