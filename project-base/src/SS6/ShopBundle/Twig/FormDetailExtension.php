<?php

namespace SS6\ShopBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormView;
use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

class FormDetailExtension extends Twig_Extension {

	/**
	 * @var ContainerInterface
	 */
	private $container;

	public function __construct(ContainerInterface $container) {
		$this->container = $container; // Must inject main container - https://github.com/symfony/symfony/issues/2347
	}

	/**
	 * Get service "templating" cannot by called in constructor - https://github.com/symfony/symfony/issues/2347
	 *
	 * @return Twig_Environment
	 */
	private function getTemplatingService() {
		return $this->container->get('templating');
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return [
			new Twig_SimpleFunction('form_id', [$this, 'formId'], ['is_safe' => ['html']]),
			new Twig_SimpleFunction('form_save', [$this, 'formSave'], ['is_safe' => ['html']]),
		];
	}

	/**
	 * @param mixed $object
	 * @return string
	 */
	public function formId($object) {
		if ($object === null) {
			return '';
		} else {
			return '<div class="form-line">
						<label class="form-line__label">ID:</label>
						<div class="form-line__side">
							<div class="form-line__item">
								<input
									type="text"
									value="' . htmlspecialchars($object->getId(), ENT_QUOTES) . '"
									class="input"
									readonly="readonly"
								>
							</div>
						</div>
					</div>';
		}
	}

	/**
	 * @param mixed $object
	 * @param \Symfony\Component\Form\FormView $formView
	 * @param array $vars
	 * @return string
	 */
	public function formSave($object, FormView $formView, array $vars = []) {
		$template = '{{ form_widget(form.save, vars) }}';

		if (!array_keys($vars, 'label', true)) {
			if ($object === null) {
				$vars['label'] = t('Vytvořit');
			} else {
				$vars['label'] = t('Uložit změny');
			}
		}

		$parameters['form'] = $formView;
		$parameters['vars'] = $vars;

		return $this->getTemplatingService()->render($template, $parameters);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'ss6.twig.form_detail_extension';
	}

}
