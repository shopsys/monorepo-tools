<?php

namespace SS6\ShopBundle\Model\Grid;

use SS6\ShopBundle\Model\Grid\ActionColumn;
use SS6\ShopBundle\Model\Grid\Column;
use SS6\ShopBundle\Model\Grid\DataSourceInterface;
use SS6\ShopBundle\Model\Grid\GridView;
use SS6\ShopBundle\Model\Grid\InlineEdit\GridInlineEditInterface;
use SS6\ShopBundle\Model\Grid\Ordering\GridOrderingService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Router;
use Twig_Environment;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Grid {

	const GET_PARAMETER = 'g';
	const DEFAULT_VIEW_THEME = '@SS6Shop/Admin/Grid/Grid.html.twig';
	const DEFAULT_LIMIT = 30;

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var \SS6\ShopBundle\Model\Grid\Column[]
	 */
	private $columns = [];

	/**
	 * @var \SS6\ShopBundle\Model\Grid\ActionColumn[]
	 */
	private $actionColumns = [];

	/**
	 * @var bool
	 */
	private $allowPaging = false;

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
	 * @var row
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

		$this->limit = self::DEFAULT_LIMIT;
		$this->page = 1;

		$this->viewTheme = self::DEFAULT_VIEW_THEME;
		$this->viewTemplateParameters = [];

		$this->loadFromRequest();
	}

	/**
	 * @param string $id
	 * @param string $sourceColumnName
	 * @param string $title
	 * @param boolean $sortable
	 * @return \SS6\ShopBundle\Model\Grid\Column
	 */
	public function addColumn($id, $sourceColumnName, $title, $sortable = false) {
		if (array_key_exists($id, $this->columns)) {
			throw new \SS6\ShopBundle\Model\Grid\Exception\DuplicateColumnIdException(
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
		return Grid::getValueFromRowBySourceColumnName($row, $this->dataSource->getRowIdSourceColumnName());
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
		$gridView =  $this->createViewWithoutRows();
		if ($this->isAllowedPaging()) {
			$this->executeTotalQuery();
		}
		$this->loadRows();

		return $gridView;
	}

	/**
	 * @param int $rowId
	 * @return \SS6\ShopBundle\Model\Grid\GridView
	 */
	public function createViewWithOneRow($rowId) {
		$gridView =  $this->createViewWithoutRows();
		$this->loadRowsWithOneRow($rowId);

		return $gridView;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Grid\GridView
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

	public function allowPaging() {
		$this->allowPaging = true;
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
	 * @return \SS6\ShopBundle\Model\Grid\Column[]
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
	 * @return \SS6\ShopBundle\Component\Paginator\PaginationResult
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
		$requestData = $this->requestStack->getMasterRequest()->get(self::GET_PARAMETER, []);
		if (array_key_exists($this->id, $requestData)) {
			$gridData = $requestData[$this->id];
			if (array_key_exists('limit', $gridData)) {
				$this->setLimit((int)trim($gridData['limit']));
				$this->isLimitFromRequest = true;
			}
			if (array_key_exists('page', $gridData)) {
				$this->page = max((int)trim($gridData['page']), 1);
			}
			if (array_key_exists('order', $gridData)) {
				$this->setOrderingByOrderString(trim($gridData['order']));
				$this->isOrderFromRequest = true;
			}
		}
	}

	/**
	 * @param array|string|null $removeParameters
	 * @return array
	 */
	public function getGridParameters($removeParameters = null) {
		$gridParameters = [];
		if ($this->isAllowedPaging()) {
			$gridParameters['limit'] = $this->getLimit();
			if ($this->getPage() > 1) {
				$gridParameters['page'] = $this->getPage();
			}
		}
		if ($this->getOrderSourceColumnName() !== null) {
			$gridParameters['order'] = $this->getOrderSourceColumnNameWithDirection();
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
			array_key_exists($this->orderSourceColumnName, $this->getColumns())
			&& $this->columns[$this->orderSourceColumnName]->isSortable()
		) {
			$orderSourceColumnName = $this->getColumns()[$this->orderSourceColumnName]->getOrderSourceColumnName();
		} else {
			$orderSourceColumnName = null;
		}

		$orderDirection = $this->orderDirection;

		if ($this->isDragAndDrop()) {
			$orderSourceColumnName = null;
			$orderDirection = null;
		}

		$this->paginationResults = $this->dataSource->getPaginatedRows(
			$this->allowPaging ? $this->limit : null,
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
			$value = $row[$sourceColumnNameParts[0]];
		} elseif (count($sourceColumnNameParts) === 2) {
			if (array_key_exists($sourceColumnNameParts[0], $row)
				&& array_key_exists($sourceColumnNameParts[1], $row[$sourceColumnNameParts[0]])
			) {
				$value = $row[$sourceColumnNameParts[0]][$sourceColumnNameParts[1]];
			} elseif (array_key_exists($sourceColumnNameParts[1], $row)) {
				$value = $row[$sourceColumnNameParts[1]];
			} else {
				$value = $row[$sourceColumnName];
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
