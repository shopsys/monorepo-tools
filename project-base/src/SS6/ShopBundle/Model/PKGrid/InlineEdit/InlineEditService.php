<?php

namespace SS6\ShopBundle\Model\PKGrid\InlineEdit;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Form;
use Twig_Environment;

class InlineEditService {

	/**
	 * @var \Symfony\Component\DependencyInjection\Container
	 */
	private $container;

	/**
	 * @var \Twig_Environment
	 */
	private $twigEnvironment;

	/**
	 * @param \Symfony\Component\DependencyInjection\Container $container
	 * @param \Twig_Environment $twigEnvironment
	 */
	public function __construct(Container $container, Twig_Environment $twigEnvironment) {
		$this->container = $container;
		$this->twigEnvironment = $twigEnvironment;
	}

		/**
	 * @param string $serviceName
	 * @param mixed $rowId
	 * @return array
	 */
	public function getFormData($serviceName, $rowId) {
		$gridInlineEdit = $this->container->get($serviceName, Container::NULL_ON_INVALID_REFERENCE);

		if ($gridInlineEdit instanceof GridInlineEditInterface) {
			$form = $gridInlineEdit->getForm($rowId);
			return $this->renderFormToArray($form);
		} else {
			throw new \SS6\ShopBundle\Model\PKGrid\InlineEdit\Exception\InvalidServiceException($serviceName);
		}
	}

	/**
	 * @param \Symfony\Component\Form\Form $form
	 * @return array
	 */
	private function renderFormToArray(Form $form) {
		$formView = $form->createView();
		$result = [];

		foreach ($formView->children as $formName => $childrenForm) {
			$result[$formName] = $this->twigEnvironment->render('{{ form_widget(form) }}', array('form' => $childrenForm));
		}

		return $result;
	}

}
