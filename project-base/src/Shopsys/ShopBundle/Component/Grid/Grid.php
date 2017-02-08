<?php

namespace Shopsys\ShopBundle\Component\Grid;

use Shopsys\ShopBundle\Component\Grid\ActionColumn;
use Shopsys\ShopBundle\Component\Grid\Column;
use Shopsys\ShopBundle\Component\Grid\DataSourceInterface;
use Shopsys\ShopBundle\Component\Grid\GridView;
use Shopsys\ShopBundle\Component\Grid\InlineEdit\GridInlineEditInterface;
use Shopsys\ShopBundle\Component\Grid\Ordering\GridOrderingService;
use Shopsys\ShopBundle\Component\Router\Security\RouteCsrfProtector;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Router;
use Twig_Environment;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Grid {

	const GET_PARAMETER = 'g';
	const DEFAULT_VIEW_THEME = '@ShopsysShop/Admin/Grid/Grid.html.twig';
	const DEFAULT_LIMIT = 30;

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var \Shopsys\ShopBundle\Component\Grid\Column[columnId]
	 */
	private $columns = [];

	/**
	 * @var \Shopsys\ShopBundle\Component\Grid\ActionColumn[actionColumnId]
	 */
	private $actionColumns = [];

	/**
	 * @var bool
	 */
	private $enablePaging = false;

	/**
	 * @var bool
	 */
	private $enableSelecting = false;

	/**
	 * @var array
	 */
	private $allowedLimits = [30, 100, 200, 500];

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
	private $orderSourceColumnName;

	/**
	 * @var string|null
	 */
	private $orderDirection;

	/**
	 * @var bool
	 */
	private $isOrderFromRequest = false;

	/**
	 * @var array
	 */
	private $rows = [];

	/**
	 * @var \Symfony\Component\HttpFoundation\RequestStack
	 */
	private $requestStack;

	/**
	 * @var \Symfony\Component\Routing\Router
	 */
	private $router;

	/**
	 * @var \Shopsys\ShopBundle\Component\Router\Security\RouteCsrfProtector
	 */
	private $routeCsrfProtector;

	/**
	 * @var \Twig_Environment
	 */
	private $twig;

	/**
	 * @var \Shopsys\ShopBundle\Component\Grid\DataSourceInterface
	 */
	private $dataSource;

	/**
	 * @var string
	 */
	private $actionColumnClassAttribute = '';

	/**
	 * @var \Shopsys\ShopBundle\Component\Grid\InlineEdit\GridInlineEditInterface|null
	 */
	private $inlineEditService;

	/**
	 * @var \Shopsys\ShopBundle\Component\Grid\Ordering\GridOrderingService
	 */
	private $gridOrderingService;

	/**
	 * @var string|null
	 */
	private $orderingEntityClass;

	/**
	 * @var \Shopsys\ShopBundle\Component\Paginator\PaginationResult
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
	 * @var array
	 */
	private $selectedRowIds;

	/**
	 * @var bool
	 */
	private $multipleDragAndDrop;

	/**
	 * @param string $id
	 * @param \Shopsys\ShopBundle\Component\Grid\DataSourceInterface $dataSource
	 * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
	 * @param \Symfony\Component\Routing\Router $router
	 * @param \Shopsys\ShopBundle\Component\Router\Security\RouteCsrfProtector $routeCsrfProtector
	 * @param \Twig_Environment $twig
	 * @param \Shopsys\ShopBundle\Component\Grid\Ordering\GridOrderingService $gridOrderingService
	 */
	public function __construct(
		$id,
		DataSourceInterface $dataSource,
		RequestStack $requestStack,
		Router $router,
		RouteCsrfProtector $routeCsrfProtector,
		Twig_Environment $twig,
		GridOrderingService $gridOrderingService
	) {
		if (empty($id)) {
			$message = 'Grid id cannot be empty.';
			throw new \Shopsys\ShopBundle\Component\Grid\Exception\EmptyGridIdException($message);
		}

		$this->id = $id;
		$this->dataSource = $dataSource;
		$this->requestStack = $requestStack;
		$this->router = $router;
		$this->routeCsrfProtector = $routeCsrfProtector;
		$this->twig = $twig;
		$this->gridOrderingService = $gridOrderingService;

		$this->limit = self::DEFAULT_LIMIT;
		$this->page = 1;

		$this->viewTheme = self::DEFAULT_VIEW_THEME;
		$this->viewTemplateParameters = [];

		$this->selectedRowIds = [];
		$this->multipleDragAndDrop = false;

		$this->loadFromRequest();
	}

	/**
	 * @param string $id
	 * @param string $sourceColumnName
	 * @param string $title
	 * @param bool $sortable
	 * @return \Shopsys\ShopBundle\Component\Grid\Column
	 */
	public function addColumn($id, $sourceColumnName, $title, $sortable = false) {
		if (array_key_exists($id, $this->columns)) {
			throw new \Shopsys\ShopBundle\Component\Grid\Exception\DuplicateColumnIdException(
				'Duplicate column id "' . $id . '" in grid "' . $this->id .  '"'
			);
		}
		$column = new Column($id, $sourceColumnName, $title, $sortable);
		$this->columns[$id] = $column;
		return $column;
	}

	/**
	 * @param string $type
	 * @param string $name
	 * @param string $route
	 * @param array $bindingRouteParams
	 * @param array $additionalRouteParams
	 * @return \Shopsys\ShopBundle\Component\Grid\ActionColumn
	 */
	public function addActionColumn(
		$type,
		$name,
		$route,
		array $bindingRouteParams = [],
		array $additionalRouteParams = []
	) {
		$actionColumn = new ActionColumn(
			$this->router,
			$this->routeCsrfProtector,
			$type,
			$name,
			$route,
			$bindingRouteParams,
			$additionalRouteParams
		);
		$this->actionColumns[] = $actionColumn;

		return $actionColumn;
	}

	/**
	 * @param string $route
	 * @param array $bindingRouteParams
	 * @param array $additionalRouteParams
	 * @return \Shopsys\ShopBundle\Component\Grid\ActionColumn
	 */
	public function addEditActionColumn($route, array $bindingRouteParams = [], array $additionalRouteParams = []) {
		return $this->addActionColumn(ActionColumn::TYPE_EDIT, t('Edit'), $route, $bindingRouteParams, $additionalRouteParams);
	}

	/**
	 * @param string $route
	 * @param array $bindingRouteParams
	 * @param array $additionalRouteParams
	 * @return \Shopsys\ShopBundle\Component\Grid\ActionColumn
	 */
	public function addDeleteActionColumn($route, array $bindingRouteParams = [], array $additionalRouteParams = []) {
		return $this->addActionColumn(ActionColumn::TYPE_DELETE, t('Delete'), $route, $bindingRouteParams, $additionalRouteParams);
	}

	/**
	 * @param \Shopsys\ShopBundle\Component\Grid\InlineEdit\GridInlineEditInterface $inlineEditService
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
	 * @return \Shopsys\ShopBundle\Component\Grid\InlineEdit\GridInlineEditInterface|null
	 */
	public function getInlineEditService() {
		return $this->inlineEditService;
	}

	/**
	 * @param array $row
	 * @return mixed
	 */
	public function getRowId($row) {
		return self::getValueFromRowBySourceColumnName($row, $this->dataSource->getRowIdSourceColumnName());
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
	 * @return \Shopsys\ShopBundle\Component\Grid\GridView
	 */
	public function createView() {
		$gridView = $this->createViewWithoutRows();
		if ($this->isEnabledPaging()) {
			$this->executeTotalQuery();
		}
		$this->loadRows();

		return $gridView;
	}

	/**
	 * @param int $rowId
	 * @return \Shopsys\ShopBundle\Component\Grid\GridView
	 */
	public function createViewWithOneRow($rowId) {
		$gridView = $this->createViewWithoutRows();
		$this->loadRowsWithOneRow($rowId);

		return $gridView;
	}

	/**
	 * @return \Shopsys\ShopBundle\Component\Grid\GridView
	 */
	public function createViewWithoutRows() {
		$this->rows = [];
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

	public function enablePaging() {
		$this->enablePaging = true;
	}

	public function enableSelecting() {
		$this->enableSelecting = true;
	}

	/**
	 * @param int $limit
	 */
	public function setDefaultLimit($limit) {
		if (!$this->isLimitFromRequest) {
			$this->setLimit((int)$limit);
		}
	}

	/**
	 * @param string $columnId
	 * @param string $direction
	 */
	public function setDefaultOrder($columnId, $direction = DataSourceInterface::ORDER_ASC) {
		if (!$this->isOrderFromRequest) {
			$prefix = $direction == DataSourceInterface::ORDER_DESC ? '-' : '';
			$this->setOrderingByOrderString($prefix . $columnId);
		}
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return \Shopsys\ShopBundle\Component\Grid\Column[]
	 */
	public function getColumns() {
		return $this->columns;
	}

	/**
	 * @return bool
	 */
	public function existsColumn($columntId) {
		return array_key_exists($columntId, $this->columns);
	}

	/**
	 * @return \Shopsys\ShopBundle\Component\Grid\ActionColumn[]
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
	public function isEnabledPaging() {
		return $this->enablePaging;
	}

	/**
	 * @return bool
	 */
	public function isEnabledSelecting() {
		return $this->enableSelecting;
	}

	/**
	 * @param array $row
	 * @return bool
	 */
	public function isRowSelected(array $row) {
		$rowId = $this->getRowId($row);
		return in_array($rowId, $this->selectedRowIds);
	}

	/**
	 * @return array
	 */
	public function getSelectedRowIds() {
		return $this->selectedRowIds;
	}

	/**
	 * @return int
	 */
	public function getLimit() {
		return $this->limit;
	}

	/**
	 * @param int $limit
	 */
	private function setLimit($limit) {
		if (in_array($limit, $this->allowedLimits)) {
			$this->limit = $limit;
		}
	}

	/**
	 * @return array
	 */
	public function getAllowedLimits() {
		return $this->allowedLimits;
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
	public function getOrderSourceColumnName() {
		return $this->orderSourceColumnName;
	}

	/**
	 * @return string|null
	 */
	public function getOrderSourceColumnNameWithDirection() {
		$prefix = '';
		if ($this->getOrderDirection() === DataSourceInterface::ORDER_DESC) {
			$prefix = '-';
		}

		return $prefix . $this->getOrderSourceColumnName();
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
	 * @return \Shopsys\ShopBundle\Component\Paginator\PaginationResult
	 */
	public function getPaginationResults() {
		return $this->paginationResults;
	}

	/**
	 * @param string $orderString
	 */
	private function setOrderingByOrderString($orderString) {
		if (substr($orderString, 0, 1) === '-') {
			$this->orderDirection = DataSourceInterface::ORDER_DESC;
		} else {
			$this->orderDirection = DataSourceInterface::ORDER_ASC;
		}
		$this->orderSourceColumnName = trim($orderString, '-');
	}

	private function loadFromRequest() {
		$queryData = $this->requestStack->getMasterRequest()->query->get(self::GET_PARAMETER, []);
		if (array_key_exists($this->id, $queryData)) {
			$gridQueryData = $queryData[$this->id];
			if (array_key_exists('limit', $gridQueryData)) {
				$this->setLimit((int)trim($gridQueryData['limit']));
				$this->isLimitFromRequest = true;
			}
			if (array_key_exists('page', $gridQueryData)) {
				$this->page = max((int)trim($gridQueryData['page']), 1);
			}
			if (array_key_exists('order', $gridQueryData)) {
				$this->setOrderingByOrderString(trim($gridQueryData['order']));
				$this->isOrderFromRequest = true;
			}
		}
		$requestData = $this->requestStack->getMasterRequest()->request->get(self::GET_PARAMETER, []);
		if (array_key_exists($this->id, $requestData)) {
			$gridRequestData = $requestData[$this->id];
			if (array_key_exists('selectedRowIds', $gridRequestData) && is_array($gridRequestData['selectedRowIds'])) {
				$this->selectedRowIds = array_map('json_decode', $gridRequestData['selectedRowIds']);
			}
		}
	}

	/**
	 * @param array|string $removeParameters
	 * @return array
	 */
	public function getGridParameters($removeParameters = []) {
		$gridParameters = [];
		if ($this->isEnabledPaging()) {
			$gridParameters['limit'] = $this->getLimit();
			if ($this->getPage() > 1) {
				$gridParameters['page'] = $this->getPage();
			}
		}
		if ($this->getOrderSourceColumnName() !== null) {
			$gridParameters['order'] = $this->getOrderSourceColumnNameWithDirection();
		}

		foreach ((array)$removeParameters as $parameterToRemove) {
			if (array_key_exists($parameterToRemove, $gridParameters)) {
				unset($gridParameters[$parameterToRemove]);
			}
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

		return [self::GET_PARAMETER => [$this->getId() => $gridParameters]];
	}

	/**
	 * @param array|string|null $parameters
	 * @param array|string|null $removeParameters
	 * @return array
	 */
	public function getUrlParameters($parameters = null, $removeParameters = null) {
		return array_replace_recursive(
			$this->requestStack->getMasterRequest()->query->all(),
			$this->requestStack->getMasterRequest()->attributes->get('_route_params'),
			$this->getUrlGridParameters($parameters, $removeParameters)
		);
	}

	private function loadRows() {
		if (
			array_key_exists($this->orderSourceColumnName, $this->columns)
			&& $this->columns[$this->orderSourceColumnName]->isSortable()
		) {
			$orderSourceColumnName = $this->columns[$this->orderSourceColumnName]->getOrderSourceColumnName();
		} else {
			$orderSourceColumnName = null;
		}

		$orderDirection = $this->orderDirection;

		if ($this->isDragAndDrop()) {
			$orderSourceColumnName = null;
			$orderDirection = null;
		}

		$this->paginationResults = $this->dataSource->getPaginatedRows(
			$this->enablePaging ? $this->limit : null,
			$this->page,
			$orderSourceColumnName,
			$orderDirection
		);

		$this->rows = $this->paginationResults->getResults();
	}

	/**
	 * @param int $rowId
	 */
	private function loadRowsWithOneRow($rowId) {
		$this->rows = [$this->dataSource->getOneRow($rowId)];
	}

	private function executeTotalQuery() {
		$this->totalCount = $this->dataSource->getTotalRowsCount();
		$this->pageCount = max(ceil($this->totalCount / $this->limit), 1);
		$this->page = min($this->page, $this->pageCount);
	}

	/**
	 * @param array $row
	 * @param string $sourceColumnName
	 * @return mixed
	 */
	public static function getValueFromRowBySourceColumnName(array $row, $sourceColumnName) {
		$sourceColumnNameParts = explode('.', $sourceColumnName);

		if (count($sourceColumnNameParts) === 1) {
			return $row[$sourceColumnNameParts[0]];
		} elseif (count($sourceColumnNameParts) === 2) {
			if (array_key_exists($sourceColumnNameParts[0], $row)
				&& array_key_exists($sourceColumnNameParts[1], $row[$sourceColumnNameParts[0]])
			) {
				return $row[$sourceColumnNameParts[0]][$sourceColumnNameParts[1]];
			} elseif (array_key_exists($sourceColumnNameParts[1], $row)) {
				return $row[$sourceColumnNameParts[1]];
			} else {
				return $row[$sourceColumnName];
			}
		}

		return $row[$sourceColumnName];
	}

	/**
	 * @param string $entityClass
	 */
	public function enableDragAndDrop($entityClass) {
		$this->orderingEntityClass = $entityClass;
	}

	public function enableMultipleDragAndDrop() {
		$this->multipleDragAndDrop = true;
	}

	/**
	 * @return bool
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

	/**
	 * @return bool
	 */
	public function isMultipleDragAndDrop() {
		return $this->multipleDragAndDrop;
	}

}
