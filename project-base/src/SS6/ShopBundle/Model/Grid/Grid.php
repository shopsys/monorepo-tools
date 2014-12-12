<?php

namespace SS6\ShopBundle\Model\Grid;

use SS6\ShopBundle\Model\Grid\ActionColumn;
use SS6\ShopBundle\Model\Grid\Column;
use SS6\ShopBundle\Model\Grid\DataSourceInterface;
use SS6\ShopBundle\Model\Grid\Ordering\GridOrderingService;
use SS6\ShopBundle\Model\Grid\InlineEdit\GridInlineEditInterface;
use SS6\ShopBundle\Model\Grid\GridView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Router;
use Twig_Environment;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Grid {

	const GET_PARAMETER = 'g';
	const DEFAULT_VIEW_THEME = '@SS6Shop/Admin/Grid/Grid.html.twig';

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var \SS6\ShopBundle\Model\Grid\Column[]
	 */
	private $columns = array();

	/**
	 * @var \SS6\ShopBundle\Model\Grid\ActionColumn[]
	 */
	private $actionColumns = array();

	/**
	 * @var bool
	 */
	private $allowPaging = false;

	/**
	 * @var array
	 */
	private $limits = array(30, 100, 200, 500);

	/**
	 * @var int
	 */
	private $limit;

	/**
	 * @var bool
	 */
	private $isLimitFromRequest = false;

	/**
	 * @var int
	 */
	private $defaultLimit = 30;

	/**
	 * @var int
	 */
	private $page = 1;

	/**
	 * @var int|null
	 */
	private $totalCount;

	/**
	 * @var int|null
	 */
	private $pageCount;

	/**
	 * @var string|null
	 */
	private $order;

	/**
	 * @var string|null
	 */
	private $orderDirection;

	/**
	 * @var bool
	 */
	private $isOrderFromRequest = false;

	/**
	 * @var row
	 */
	private $rows = array();

	/**
	 * @var \Symfony\Component\HttpFoundation\RequestStack
	 */
	private $requestStack;

	/**
	 * @var \Symfony\Component\Routing\Router
	 */
	private $router;

	/**
	 * @var \Twig_Environment
	 */
	private $twig;

	/**
	 * @var \SS6\ShopBundle\Model\Grid\DataSourceInterface
	 */
	private $dataSource;

	/**
	 * @var string
	 */
	private $actionColumnClassAttribute = '';

	/**
	 * @var \SS6\ShopBundle\Model\Grid\InlineEdit\GridInlineEditInterface|null
	 */
	private $inlineEditService;

	/**
	 * @var \SS6\ShopBundle\Model\Grid\Ordering\GridOrderingService
	 */
	private $gridOrderingService;

	/**
	 * @var string|null
	 */
	private $orderingEntityClass;

	/**
	 * @var \SS6\ShopBundle\Component\Paginator\PaginationResult
	 */
	private $paginationResults;

	/**
	 * @var string|string[]|null
	 */
	private $viewTheme;

	/**
	 * @var array
	 */
	private $viewTemplateParameters;

	/**
	 * @param string $id
	 * @param \SS6\ShopBundle\Model\Grid\DataSourceInterface $dataSource
	 * @param \SS6\ShopBundle\Model\Grid\RequestStack $requestStack
	 * @param \SS6\ShopBundle\Model\Grid\Router $router
	 * @param \SS6\ShopBundle\Model\Grid\Twig_Environment $twig
	 * @param \SS6\ShopBundle\Model\Grid\Ordering\GridOrderingService $gridOrderingService
	 */
	public function __construct(
		$id,
		DataSourceInterface $dataSource,
		RequestStack $requestStack,
		Router $router,
		Twig_Environment $twig,
		GridOrderingService $gridOrderingService
	) {
		if (empty($id)) {
			$message = 'Grid id cannot be empty.';
			throw new \SS6\ShopBundle\Model\Grid\Exception\EmptyGridIdException($message);
		}

		$this->id = $id;
		$this->dataSource = $dataSource;
		$this->requestStack = $requestStack;
		$this->router = $router;
		$this->twig = $twig;
		$this->gridOrderingService = $gridOrderingService;

		$this->limit = $this->defaultLimit;
		$this->page = 1;

		$this->viewTheme = self::DEFAULT_VIEW_THEME;
		$this->viewTemplateParameters = [];

		$this->loadFromRequest();
	}

	/**
	 * @param string $id
	 * @param string $queryId
	 * @param string $title
	 * @param boolean $sortable
	 * @return \SS6\ShopBundle\Model\Grid\Column
	 */
	public function addColumn($id, $queryId, $title, $sortable = false) {
		if (array_key_exists($id, $this->columns)) {
			throw new \SS6\ShopBundle\Model\Grid\Exception\DuplicateColumnIdException(
				'Duplicate column id "' . $id . '" in grid "' . $this->id .  '"'
			);
		}
		$column = new Column($id, $queryId, $title, $sortable);
		$this->columns[$id] = $column;
		return $column;
	}

	/**
	 * @param string $type
	 * @param string $name
	 * @param string $route
	 * @param array $bindingRouteParams
	 * @param array $additionalRouteParams
	 * @return \SS6\ShopBundle\Model\Grid\ActionColumn
	 */
	public function addActionColumn($type, $name, $route, array $bindingRouteParams = null,
		array $additionalRouteParams = null
	) {
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

	/**
	 * @param \SS6\ShopBundle\Model\Grid\InlineEdit\GridInlineEditInterface $inlineEditService
	 */
	public function setInlineEditService(GridInlineEditInterface $inlineEditService) {
		$this->inlineEditService = $inlineEditService;
	}

	/**
	 * @return bool
	 */
	public function isInlineEdit() {
		return $this->inlineEditService !== null;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Grid\InlineEdit\GridInlineEditInterface|null
	 */
	public function getInlineEditService() {
		return $this->inlineEditService;
	}

	/**
	 * @param array $row
	 * @return mixed
	 */
	public function getRowId($row) {
		return Grid::getValueFromRowByQueryId($row, $this->dataSource->getIdQueryId());
	}

	/**
	 * @param string $classAttribute
	 */
	public function setActionColumnClassAttribute($classAttribute) {
		$this->actionColumnClassAttribute = $classAttribute;
	}

	/**
	 * @param string|string[] $viewTheme
	 * @param array $viewParameters
	 */
	public function setTheme($viewTheme, array $viewParameters = []) {
		$this->viewTheme = $viewTheme;
		$this->viewTemplateParameters = $viewParameters;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Grid\GridView
	 */
	public function createView() {
		if ($this->isAllowedPaging()) {
			$this->executeTotalQuery();
		}
		$this->loadRows();
		$gridView = new GridView(
			$this,
			$this->requestStack,
			$this->router,
			$this->twig,
			$this->viewTheme,
			$this->viewTemplateParameters
		);

		return $gridView;
	}

	/**
	 * @param int $rowId
	 * @return \SS6\ShopBundle\Model\Grid\GridView
	 */
	public function createViewWithOneRow($rowId) {
		$this->loadRowsWithOneRow($rowId);
		$gridView = new GridView(
			$this,
			$this->requestStack,
			$this->router,
			$this->twig,
			$this->viewTheme,
			$this->viewTemplateParameters
		);

		return $gridView;
	}

	public function allowPaging() {
		$this->allowPaging = true;
	}

	/**
	 * @param int $limit
	 */
	public function setDefaultLimit($limit) {
		if (!$this->isLimitFromRequest) {
			$this->limit = (int)$limit;
		}
	}

	/**
	 * @param string $columnId
	 * @param string $direction
	 */
	public function setDefaultOrder($columnId, $direction = DataSourceInterface::ORDER_ASC) {
		if (!$this->isOrderFromRequest) {
			$prefix = $direction == DataSourceInterface::ORDER_DESC ? '-' : '';
			$this->setOrder($prefix . $columnId);
		}
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Grid\Column[]
	 */
	public function getColumns() {
		return $this->columns;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Grid\ActionColumn[]
	 */
	public function getActionColumns() {
		return $this->actionColumns;
	}

	/**
	 * @return array
	 */
	public function getRows() {
		return $this->rows;
	}

	/**
	 * @return bool
	 */
	public function isAllowedPaging() {
		return $this->allowPaging;
	}

	/**
	 * @return int
	 */
	public function getLimit() {
		return $this->limit;
	}

	/**
	 * @return array
	 */
	public function getLimits() {
		return $this->limits;
	}

	/**
	 * @return int|null
	 */
	public function getTotalCount() {
		return $this->totalCount;
	}

	/**
	 * @return int
	 */
	public function getPage() {
		return $this->page;
	}

	/**
	 * @return int
	 */
	public function getPageCount() {
		return $this->pageCount;
	}

	/**
	 * @return string|null
	 */
	public function getOrder() {
		return $this->order;
	}

	/**
	 * @return string|null
	 */
	public function getOrderWithDirection() {
		$prefix = '';
		if ($this->getOrderDirection() === DataSourceInterface::ORDER_DESC) {
			$prefix = '-';
		}

		return $prefix . $this->getOrder();
	}

	/**
	 * @return string|null
	 */
	public function getOrderDirection() {
		return $this->orderDirection;
	}

	/**
	 * @return string
	 */
	public function getActionColumnClassAttribute() {
		return $this->actionColumnClassAttribute;
	}

	/**
	 * @return \SS6\ShopBundle\Component\Paginator\PaginationResult
	 */
	public function getPaginationResults() {
		return $this->paginationResults;
	}

	/**
	 * @param string $orderString
	 */
	private function setOrder($orderString) {
		if (substr($orderString, 0, 1) === '-') {
			$this->orderDirection = DataSourceInterface::ORDER_DESC;
		} else {
			$this->orderDirection = DataSourceInterface::ORDER_ASC;
		}
		$this->order = trim($orderString, '-');
	}

	private function loadFromRequest() {
		$requestData = $this->requestStack->getMasterRequest()->get(self::GET_PARAMETER, array());
		if (array_key_exists($this->id, $requestData)) {
			$gridData = $requestData[$this->id];
			if (array_key_exists('limit', $gridData)) {
				$this->limit = max((int)trim($gridData['limit']), 1);
				$this->isLimitFromRequest = true;
			}
			if (array_key_exists('page', $gridData)) {
				$this->page = max((int)trim($gridData['page']), 1);
			}
			if (array_key_exists('order', $gridData)) {
				$this->setOrder(trim($gridData['order']));
				$this->isOrderFromRequest = true;
			}
		}
	}

	/**
	 * @param array|string|null $removeParameters
	 * @return array
	 */
	public function getGridParameters($removeParameters = null) {
		$gridParameters = array();
		if ($this->isAllowedPaging()) {
			$gridParameters['limit'] = $this->getLimit();
			if ($this->getPage() > 1) {
				$gridParameters['page'] = $this->getPage();
			}
		}
		if ($this->getOrder() !== null) {
			$gridParameters['order'] = $this->getOrderWithDirection();
		}

		foreach ((array)$removeParameters as $parameterToRemove) {
			// trigger notice when typo
			unset($gridParameters[$parameterToRemove]);
		}
		return $gridParameters;
	}

	/**
	 * @param array|string|null $parameters
	 * @param array|string|null $removeParameters
	 * @return array
	 */
	public function getUrlGridParameters($parameters = null, $removeParameters = null) {
		$gridParameters = array_replace_recursive(
			$this->getGridParameters($removeParameters),
			(array)$parameters
		);

		return array(self::GET_PARAMETER => array($this->getId() => $gridParameters));
	}

	/**
	 * @param array|string|null $parameters
	 * @param array|string|null $removeParameters
	 * @return array
	 */
	public function getUrlParameters($parameters = null, $removeParameters = null) {
		return array_replace_recursive(
			$this->requestStack->getMasterRequest()->query->all(),
			$this->getUrlGridParameters($parameters, $removeParameters)
		);
	}

	private function loadRows() {
		if (array_key_exists($this->order, $this->getColumns())) {
			$orderQueryId = $this->getColumns()[$this->order]->getQueryOrderId();
		} else {
			$orderQueryId = null;
		}

		$orderDirection = $this->orderDirection;

		if ($this->isDragAndDrop()) {
			$orderQueryId = null;
			$orderDirection = null;
		}

		$this->paginationResults = $this->dataSource->getPaginatedRows(
			$this->allowPaging ? $this->limit : null,
			$this->page,
			$orderQueryId,
			$orderDirection
		);

		$this->rows = $this->paginationResults->getResults();
	}

	/**
	 * @param string $queryId
	 * @param int $rowId
	 */
	private function loadRowsWithOneRow($rowId) {
		$this->rows = array($this->dataSource->getOneRow($rowId));
	}

	private function executeTotalQuery() {
		$this->totalCount = $this->dataSource->getTotalRowsCount();
		$this->pageCount = max(ceil($this->totalCount / $this->limit), 1);
		$this->page = min($this->page, $this->pageCount);
	}

	/**
	 * @param array $row
	 * @param string $queryId
	 * @return mixed
	 */
	public static function getValueFromRowByQueryId(array $row, $queryId) {
		$queryIdParts = explode('.', $queryId);

		if (count($queryIdParts) === 1) {
			$value = $row[$queryIdParts[0]];
		} elseif (count($queryIdParts) === 2) {
			if (array_key_exists($queryIdParts[0], $row) && array_key_exists($queryIdParts[1], $row[$queryIdParts[0]])) {
				$value = $row[$queryIdParts[0]][$queryIdParts[1]];
			} elseif (array_key_exists($queryIdParts[1], $row)) {
				$value = $row[$queryIdParts[1]];
			} else {
				$value = $row[$queryId];
			}
		}

		return $value;
	}

	/**
	 * @param string $entityClass
	 */
	public function enableDragAndDrop($entityClass) {
		$this->orderingEntityClass = $entityClass;
	}

	/**
	 * @return boolean
	 */
	public function isDragAndDrop() {
		return $this->orderingEntityClass !== null;
	}

	/**
	 * @return string|null
	 */
	public function getOrderingEntityClass() {
		return $this->orderingEntityClass;
	}

}
