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
	 * @param array $params
	 * @param array|string|null $removeParams
	 * @return string
	 */
	public function getUrl(array $params = null, $removeParams = null) {
		$removeParams = (array)$removeParams;
		$oldRouteParams = $this->request->attributes->get('_route_params');

		$gridParams = array_replace_recursive($this->getGridAttrs(), $params);
		foreach ($gridParams as $key => $paramValue) {
			if (in_array($key, $removeParams)) {
				unset($gridParams[$key]);
			}
		}
		
		$routeParams = array_replace_recursive(
			array('q' => array($this->grid->getId() => $gridParams)),
			$oldRouteParams
		);
		$url = $this->router->generate($this->request->attributes->get('_route'), $routeParams, true);

		return $url;
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
	 * @return array
	 */
	private function getGridAttrs() {
		$gridData = array();
		if ($this->grid->isAllowedPaging()) {
			$gridData['limit'] = $this->grid->getLimit();
			if ($this->grid->getPage() > 1) {
				$gridData['page'] = $this->grid->getPage();
			}
		}
		if ($this->grid->getOrder() !== null) {
			$gridData['order'] = $this->grid->getOrder();
		}
		return $gridData;
	}

	/**
	 * @param \SS6\ShopBundle\Model\PKGrid\Column $column
	 * @param array $row
	 * @return mixed
	 */
	private function getCellValue(Column $column, $row) {
		$value = null;
		$queryIdParts = explode('.', $column->getQueryId());
		$columnIndex = array_pop($queryIdParts);

		if (array_key_exists($columnIndex, $row)) {
			$value = $row[$columnIndex];
		}

		return $value;
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
