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

	/**
	 * @param ContainerInterface $container
	 */
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
		return array(
			new Twig_SimpleFunction('form_id', array($this, 'formId'), array('is_safe' => array('html'))),
			new Twig_SimpleFunction('form_save', array($this, 'formSave'), array('is_safe' => array('html'))),
			new Twig_SimpleFunction('detailDomain', array($this, 'formDetailDomain'), array('is_safe' => array('html'))),
		);
	}

	/**
	 * @param mixed $object
	 * @retrun string
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
	 * @param \Symfony\Component\Form\FormView $form
	 * @return string
	 */
	public function formSave($object, FormView $form) {
		$template = '{{ form_widget(form.save, { label: label }) }}';
		$parameters = array('form' => $form, 'label' => 'Vytvořit');
		if ($object === null) {
			return $this->getTemplatingService()->render($template, $parameters);
		} else {
			$parameters['label'] = 'Uložit změny';
			return $this->getTemplatingService()->render($template, $parameters);
		}
	}

	/**
	 * @param mixed $object
	 * @return string
	 */
	public function formDetailDomain($object, FormView $form) {
		if ($object === null) {
			$template = '{{ form_row(form.userData.domainId, { label: label }) }}';
			$paramters = array('form' => $form, 'label' => 'Doména');
			return $this->getTemplatingService()->render($template, $paramters);
		}
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'ss6.twig.form_detail_extension';
	}

}
