<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Component\Translation\Translator;
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

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	public function __construct(
		ContainerInterface $container,
		Translator $translator
	) {
		$this->container = $container; // Must inject main container - https://github.com/symfony/symfony/issues/2347
		$this->translator = $translator;
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
						<input
							type="text"
							value="' . htmlspecialchars($object->getId(), ENT_QUOTES) . '"
							class="form-control form-line__field"
							readonly="readonly"
						>
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
				$vars['label'] = $this->translator->trans('Vytvořit');
			} else {
				$vars['label'] = $this->translator->trans('Uložit změny');
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
