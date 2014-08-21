<?php

namespace SS6\ShopBundle\Model\PKGrid;

use SS6\ShopBundle\Model\PKGrid\PKGrid;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Router;
use Twig_Environment;

class PKGridView {

	/**
	 * @var \SS6\ShopBundle\Model\PKGrid\PKGrid
	 */
	private $grid;

	/**
	 * @var array
	 */
	private $templateParameters;

	/**
	 * @var \Twig_Template[]
	 */
	private $templates;

	/**
	 * @var string|array
	 */
	private $theme;

	/**
	 * @var \Symfony\Component\HttpFoundation\Request
	 */
	private $request;

	/**
	 * @var \Symfony\Component\Routing\Router
	 */
	private $router;

	/**
	 * @var \Twig_Environment
	 */
	private $twig;

	/**
	 * @param \SS6\ShopBundle\Model\PKGrid\PKGrid $grid
	 * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
	 * @param \Symfony\Component\Routing\Router $router
	 * @param Twig_Environment $twig
	 */
	public function __construct(PKGrid $grid, RequestStack $requestStack, Router $router, Twig_Environment $twig) {
		$this->grid = $grid;
		$this->request = $requestStack->getMasterRequest();
		$this->router = $router;
		$this->twig = $twig;
	}

	/**
	 * @param string|array $theme
	 * @param array|null $parameters
	 */
	public function render($theme, array $parameters = null) {
		$this->theme = $theme;
		$this->templateParameters = array_merge(
			(array)$parameters,
			array(
				'gridView' => $this,
				'grid' => $this->grid,
			)
		);
		$this->renderBlock('pkgrid');
	}

	/**
	 * @param array|null $removeParameters
	 */
	public function renderHiddenInputs($removeParameters = null) {
		$this->renderBlock('pkgrid_hidden_inputs', array(
			'parameter' => $this->grid->getUrlGridParameters(null, $removeParameters)
		));
	}

	/**
	 * @param string $name
	 * @param array|null $parameters
	 */
	public function renderBlock($name, $parameters = null) {
		foreach ($this->getTemplates() as $template) {
			if ($template->hasBlock($name)) {
				$templateParameters = array_merge($this->twig->getGlobals(), (array)$parameters, $this->templateParameters);
				echo $template->renderBlock($name, $templateParameters);
				return;
			}
		}

		throw new \InvalidArgumentException(sprintf('Block "%s" doesn\'t exist in grid template "%s".', $name, $this->theme));
	}

	/**
	 * @param \SS6\ShopBundle\Model\PKGrid\Column $column
	 * @param array $row
	 */
	public function renderCell(Column $column, array $row) {
		$value = $this->getCellValue($column, $row);

		$posibleBlocks = array(
			'pkgrid_value_cell_id_' . $column->getId(),
			'pkgrid_value_cell_type_' . $this->getVariableType($value),
			'pkgrid_value_cell'
		);
		foreach ($posibleBlocks as $blockName) {
			if ($this->blockExists($blockName)) {
				$this->renderBlock($blockName, array('value' => $value, 'row' => $row));
				break;
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\PKGrid\ActionColumn $actionColumn
	 * @param array $row
	 */
	public function renderActionCell(ActionColumn $actionColumn, array $row) {
		$posibleBlocks = array(
			'pkgrid_action_cell_type_' . $actionColumn->getType(),
			'pkgrid_action_cell',
		);
		foreach ($posibleBlocks as $blockName) {
			if ($this->blockExists($blockName)) {
				$this->renderBlock($blockName, array('actionColumn' => $actionColumn, 'row' => $row));
				break;
			}
		}
	}

	/**
	 * @param array $parameters
	 * @param array|string|null $removeParameters
	 * @return string
	 */
	public function getUrl(array $parameters = null, $removeParameters = null) {
		$routeParameters = $this->grid->getUrlParameters($parameters, $removeParameters);

		return $this->router->generate($this->request->attributes->get('_route'), $routeParameters, true);
	}

	/**
	 * @param string $name
	 * @return boolean
	 */
	private function blockExists($name) {
		foreach ($this->getTemplates() as $template) {
			if ($template->hasBlock($name)) {
				return true;
			}
		}
		
		return false;
	}

	/**
	 * @return \Twig_Template[]
	 */
	private function getTemplates() {
		if (empty($this->templates)) {
			$this->templates = array();
			if (is_array($this->theme)) {
				foreach ($this->theme as $theme) {
					$this->templates += $this->getTemplatesFromString($theme);
				}
			} else {
				$this->templates = $this->getTemplatesFromString($this->theme);
			}
		}

		return $this->templates;
	}

	/**
	 * @param string $theme
	 * @return \Twig_Template[]
	 */
	private function getTemplatesFromString($theme) {
		$templates = array();

		$template = $this->twig->loadTemplate($theme);
		while ($template != null) {
			$templates[] = $template;
			$template = $template->getParent(array());
		}

		return $templates;
	}

	/**
	 * @param \SS6\ShopBundle\Model\PKGrid\Column $column
	 * @param array $row
	 * @return mixed
	 */
	private function getCellValue(Column $column, $row) {
		return PKGrid::getValueFromRowByQueryId($row, $column->getQueryId());
	}

	/**
	 * @param mixed $variable
	 * @return string
	 */
	private function getVariableType($variable) {
		switch (gettype($variable)) {
			case 'boolean':
				return 'boolean';
			case 'integer':
			case 'double':
				return 'number';
			case 'object':
				return str_replace('\\', '_', get_class($variable));
			case 'string':
				return 'string';
			case 'NULL':
				return 'null';
			default:
				return 'unknown';
		}
	}

}
