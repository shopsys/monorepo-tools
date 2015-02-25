<?php

namespace SS6\GeneratorBundle\Model;

use SS6\GeneratorBundle\Model\GeneratorInterface;
use Twig_Environment;

abstract class AbstractGenerator implements GeneratorInterface {

	/**
	 * @var \Twig_Environment
	 */
	protected $twig;

	/**
	 * @param \Twig_Environment $twig
	 */
	public function setTwig(Twig_Environment $twig) {
		$this->twig = $twig;
	}

	/**
	 * @param string $template
	 * @param string $target
	 * @param array $parameters
	 * @return bool
	 */
	protected function renderFile($template, $target, array $parameters) {
		if (file_exists($target)) {
			throw new \SS6\GeneratorBundle\Model\Exception\GeneratorTargetFileAlreadyExistsExpception($target);
		}
		if (!is_dir(dirname($target))) {
			mkdir(dirname($target), 0777, true);
		}

		return is_int(file_put_contents($target, $this->twig->render($this->getName() . '/' . $template, $parameters)));
	}

}
