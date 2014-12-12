<?php

namespace SS6\ShopBundle\Model\Grid\InlineEdit;

use SS6\ShopBundle\Model\Form\MultipleFormSetting;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
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
	 * @var \SS6\ShopBundle\Model\Form\MultipleFormSetting
	 */
	private $multipleFormSettings;

	/**
	 * @param \Symfony\Component\DependencyInjection\Container $container
	 * @param \Twig_Environment $twigEnvironment
	 * @param \SS6\ShopBundle\Model\Form\MultipleFormSetting $multipleFormSetting
	 */
	public function __construct(
		Container $container,
		Twig_Environment $twigEnvironment,
		MultipleFormSetting $multipleFormSetting
	) {
		$this->container = $container;
		$this->twigEnvironment = $twigEnvironment;
		$this->multipleFormSettings = $multipleFormSetting;
	}

	/**
	 * @param string $serviceName
	 * @param mixed $rowId
	 * @return array
	 */
	public function getRenderedFormWidgets($serviceName, $rowId) {
		$gridInlineEdit = $this->getInlineEditService($serviceName);
		$form = $gridInlineEdit->getForm($rowId);

		return $this->renderFormToArray($form);
	}

	/**
	 * @param string $serviceName
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param mixed $rowId
	 * @return mixed
	 */
	public function saveFormData($serviceName, Request $request, $rowId) {
		$gridInlineEdit = $this->getInlineEditService($serviceName);
		return $gridInlineEdit->saveForm($request, $rowId);
	}

	/**
	 * @param string $serviceName
	 * @param mixed $rowId
	 */
	public function getRenderedRowHtml($serviceName, $rowId) {
		$gridInlineEdit = $this->getInlineEditService($serviceName);
		$grid = $gridInlineEdit->getGrid();
		/* @var $grid \SS6\ShopBundle\Model\Grid\Grid */

		$gridView = $grid->createViewWithOneRow($rowId);
		$rows = $grid->getRows();
		$rowData = array_pop($rows);
		return $gridView->renderBlock('grid_row', array(
			'loopIndex' => 0,
			'lastRow' => false,
			'row' => $rowData,
			'emptyRow' => false,
		), false);
	}

	/**
	 * @param string $serviceName
	 * @return \SS6\ShopBundle\Model\Grid\InlineEdit\GridInlineEditInterface
	 */
	private function getInlineEditService($serviceName) {
		$gridInlineEdit = $this->container->get($serviceName, Container::NULL_ON_INVALID_REFERENCE);

		if ($gridInlineEdit instanceof GridInlineEditInterface) {
			return $gridInlineEdit;
		} else {
			throw new \SS6\ShopBundle\Model\Grid\InlineEdit\Exception\InvalidServiceException($serviceName);
		}
	}

	/**
	 * @param \Symfony\Component\Form\Form $form
	 * @return array
	 */
	private function renderFormToArray(Form $form) {
		$formView = $form->createView();
		$result = [];

		$this->multipleFormSettings->currentFormIsMultiple();
		foreach ($formView->children as $formName => $childrenForm) {
			$result[$formName] = $this->twigEnvironment->render(
				'{% form_theme form \'@SS6Shop/Admin/Form/theme.html.twig\' %}{{ form_widget(form) }}',
				array('form' => $childrenForm)
			);
		}
		$this->multipleFormSettings->reset();

		return $result;
	}

}
