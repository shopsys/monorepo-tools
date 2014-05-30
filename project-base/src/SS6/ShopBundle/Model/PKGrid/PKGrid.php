<?php

namespace SS6\ShopBundle\Model\PKGrid;

use Doctrine\ORM\QueryBuilder;

class PKGrid {

	/**
	 * @var \Symfony\Component\DependencyInjection\Container
	 */
	private $container;

	/**
	 * @var string
	 */
	private $id;

	private $columns = array();

	private $actionColumns = array();

	private $limits = array(30, 100, 200, 500);

	private $limit;

	private $page;

	private $defaultLimit = 30;

	private $templateParameters;

	private $order;

	private $orderDirection;

	private $templates;

	private $theme;

	private $rows = array();

	private $totalCount;

	private $pageCount;

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
	protected $environment;

	/**
	 * @var \Doctrine\ORM\QueryBuilder
	 */
	protected $queryBuilder;

	private $groupBy;

	public function __construct(\Symfony\Component\DependencyInjection\Container $container, $id) {
		$this->container = $container;
		$this->id = $id;
		$requestStack = $this->container->get('request_stack');
		/* @var $requestStack \Symfony\Component\HttpFoundation\RequestStack */
		$this->request = $requestStack->getMasterRequest();
		$this->router = $this->container->get('router');
		$this->environment = $this->container->get('twig');

		$this->loadFromRequest();
	}
	
	private function setOrder($order) {
		if (substr($order, 0, 1) === '-') {
			$this->orderDirection = 'desc';
		} else {
			$this->orderDirection = 'asc';
		}
		$this->order = trim($order, '-');
	}

	private function loadFromRequest() {
		$requestData = $this->request->get('q', array());
		if (array_key_exists($this->id, $requestData)) {
			$gridData = $requestData[$this->id];
			if (array_key_exists('limit', $gridData)) {
				$this->limit = (int)$gridData['limit'];
			}
			if (array_key_exists('page', $gridData)) {
				$this->page = (int)$gridData['page'];
			}
			if (array_key_exists('order', $gridData)) {
				$this->setOrder($gridData['order']);
			}
		}
	}

	public function allowPaging() {
		$this->limit = $this->limit ?: $this->defaultLimit;
		$this->page = $this->page ?: 1;
	}

	public function setQueryBuilder(QueryBuilder $queryBuilder, $groupBy) {
		$this->queryBuilder = $queryBuilder;
		$this->groupBy = $groupBy;
	}

	public function getGridResponse($view = null, $customParameters = null, Response $response = null) {

		$this->executeQuery();
		$this->executeTotalQuery();

		$this->templateParameters = array_merge(array('grid' => $this), (array)$customParameters);

		if ($view === null) {
			return $this->templateParameters;
		} else {
			return $this->container->get('templating')->renderResponse($view, $this->templateParameters, $response);
		}
	}

	private function prepareQuery() {
		if ($this->limit > 0) {
			$this->queryBuilder
				->setFirstResult($this->limit * ($this->page - 1))
				->setMaxResults($this->limit);
		}
		if ($this->order) {
			$this->queryBuilder
				->orderBy($this->columns[$this->order]->getQueryId(), $this->orderDirection);
		}
	}

	private function prepareTotalQuery() {
		if ($this->limit > 0) {
			$this->queryBuilder
				->select('COUNT(' . $this->groupBy . ') AS totalCount')
				->setFirstResult(null)
				->setMaxResults(null)
				->resetDQLPart('orderBy')
				->resetDQLPart('groupBy');
		}
	}

	private function executeQuery() {
		$this->prepareQuery();
		$this->rows = $this->queryBuilder->getQuery()->getArrayResult();
	}

	private function executeTotalQuery() {
		$this->prepareTotalQuery();
		$this->totalCount = $this->queryBuilder->getQuery()->getSingleScalarResult();
		$this->pageCount = ceil($this->totalCount / $this->limit);
		$this->page = min($this->page, $this->pageCount);
	}

	public function render($theme) {
		$this->theme = $theme;
		$this->renderBlock('pkgrid', $this->templateParameters);
	}

	public function getPageCount() {
		return $this->pageCount;
	}

	public function getId() {
		return $this->id;
	}
	public function getOrder() {
		return $this->order;
	}

	public function getOrderDirection() {
		return $this->orderDirection;
	}

	public function getColumns() {
		return $this->columns;
	}
	public function getActionColumns() {
		return $this->actionColumns;
	}

	public function getLimit() {
		return $this->limit;
	}

	public function getRows() {
		return $this->rows;
	}

	public function getTotalCount() {
		return $this->totalCount;
	}

	public function getPage() {
		return $this->page;
	}

	public function getLimits() {
		return $this->limits;
	}

	private function blockExists($name) {
		foreach ($this->getTemplates() as $template) {
			if ($template->hasBlock($name)) {
				return true;
			}
		}
		
		return false;
	}
	
	public function renderBlock($name, $parameters = null) {
		foreach ($this->getTemplates() as $template) {
			if ($template->hasBlock($name)) {
				$templateParameters = array_merge($this->environment->getGlobals(), (array)$parameters, $this->templateParameters);
				echo $template->renderBlock($name, $templateParameters);
				return;
			}
		}

		throw new \InvalidArgumentException(sprintf('Block "%s" doesn\'t exist in grid template "%s".', $name, $this->theme));
	}

	protected function getTemplates() {
		if (empty($this->templates)) {
			if ($this->theme instanceof \Twig_Template) {
				$this->templates[] = $this->theme;
				$this->templates[] = $this->environment->loadTemplate(static::DEFAULT_TEMPLATE);
			} elseif (is_string($this->theme)) {
				$this->templates = $this->getTemplatesFromString($this->theme);
			} elseif ($this->theme === null) {
				$this->templates[] = $this->environment->loadTemplate(static::DEFAULT_TEMPLATE);
			} else {
				throw new \Exception('Unable to load template');
			}
		}

		return $this->templates;
	}

	protected function getTemplatesFromString($theme) {
		$this->templates = array();

		$template = $this->environment->loadTemplate($theme);
		while ($template != null) {
			$this->templates[] = $template;
			$template = $template->getParent(array());
		}

		return $this->templates;
	}

	public function getUrl($attrs) {
		$oldRouteParams = $this->request->attributes->get('_route_params');
		$gridParams = array('q' => array(
			$this->id => $this->getGridAttrs((array)$attrs),
		));
		$routeParams = array_replace_recursive($oldRouteParams, $gridParams);
		$url = $this->router->generate($this->request->attributes->get('_route'), $routeParams, true);
		return $url;
	}

	private function getGridAttrs(array $attrs) {
		$gridData = array();
		if ($this->limit !== null) {
			$gridData['limit'] = $this->limit;
		}
		if ($this->page > 1) {
			$gridData['page'] = $this->page;
		}
		if ($this->order !== null) {
			$gridData['order'] = $this->order;
		}
		return array_replace_recursive($gridData, $attrs);
	}

	/**
	 * @return \SS6\ShopBundle\Model\PKGrid\Column
	 */
	public function addColumn($id, $queryId, $title, $sortable = false) {
		if (array_key_exists($id, $this->columns)) {
			throw new \Exception('Duplicate column id "' . $id . '" in grid "' . $this->id .  '"');
		}
		$column = new Column($id, $queryId, $title, $sortable);
		$this->columns[$id] = $column;
		return $column;
	}

	/**
	 * @return \SS6\ShopBundle\Model\PKGrid\ActionColumn
	 */
	public function addActionColumn($type, $name, $route, $bindingRouteParams, $additionalRouteParams = null) {
		$actionColumn = new ActionColumn(
			$this->router,
			$type,
			$name,
			$route,
			(array)$bindingRouteParams,
			(array)$additionalRouteParams
		);
		$this->actionColumns[] = $actionColumn;
		return $actionColumn;
	}

	public function renderCell(Column $column, array $row) {
		$value = null;
		$queryIdParts = explode('.', $column->getQueryId());
		$columnIndex = array_pop($queryIdParts);
		if (array_key_exists($columnIndex, $row)) {
			$value = $row[$columnIndex];
		}

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
