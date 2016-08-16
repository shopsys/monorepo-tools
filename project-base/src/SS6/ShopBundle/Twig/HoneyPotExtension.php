<?php

namespace SS6\ShopBundle\Twig;

use Symfony\Component\Form\FormView;
use Twig_Extension;
use Twig_SimpleFunction;

class HoneyPotExtension extends Twig_Extension {

	const PASSWORD_FIELD_NAME = 'password';

	/**
	 * @return array
	 */
	public function getFunctions() {
		return [
			new Twig_SimpleFunction(
				'formHoneyPotCheckPasswordAlreadyRendered',
				[$this, 'formHoneyPotCheckPasswordAlreadyRendered']
			),
		];
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'honey_pot';
	}

	/**
	 * @return bool
	 */
	public function formHoneyPotCheckPasswordAlreadyRendered(FormView $formView) {
		$rootFormView = $this->getRootFormView($formView);

		if ($this->containsNotRenderedPassword($rootFormView)) {
			throw new \SS6\ShopBundle\Twig\Exception\HoneyPotRenderedBeforePasswordException();
		}
	}

	/**
	 * @param \Symfony\Component\Form\FormView $formView
	 * @return \Symfony\Component\Form\FormView
	 */
	private function getRootFormView(FormView $formView) {
		$rootFormView = $formView;

		while ($rootFormView->parent !== null) {
			$rootFormView = $rootFormView->parent;
		}

		return $rootFormView;
	}

	/**
	 * @param \Symfony\Component\Form\FormView $formView
	 * @return bool
	 */
	private function containsNotRenderedPassword(FormView $formView) {
		foreach ($formView->children as $childForm) {
			if (strpos($childForm->vars['name'], self::PASSWORD_FIELD_NAME) !== false && !$childForm->isRendered()) {
				return true;
			} elseif ($this->containsNotRenderedPassword($childForm)) {
				return true;
			}
		}

		return false;
	}

}
