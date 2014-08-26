<?php

namespace SS6\ShopBundle\Model\PKGrid;

use Doctrine\DBAL\SQLParserUtils;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMapping;
use SS6\ShopBundle\Model\PKGrid\ActionColumn;
use SS6\ShopBundle\Model\PKGrid\Column;
use SS6\ShopBundle\Model\PKGrid\PKGridView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Router;
use Twig_Environment;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class PKGrid {

	const GET_PARAMETER = 'g';

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var \SS6\ShopBundle\Model\PKGrid\Column[]
	 */
	private $columns = array();

	/**
	 * @var \SS6\ShopBundle\Model\PKGrid\ActionColumn[]
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
	 * @var \Doctrine\ORM\QueryBuilder
	 */
	private $queryBuilder;

	/**
	 * @var \Doctrine\ORM\QueryBuilder
	 */
	private $queryBuilderWithOneRow;

	/**
	 * @var \Doctrine\ORM\NativeQuery
	 */
	private $totalNativeQuery;

	/**
	 * @var string
	 */
	private $actionColumnClass = '';

	/**
	 * @var string|null
	 */
	private $inlineEditServiceName;

	/**
	 * @var string|null
	 */
	private $inlineEditQueryId;

	/**
	 * @param string $id
	 * @param \SS6\ShopBundle\Model\PKGrid\RequestStack $requestStack
	 * @param \SS6\ShopBundle\Model\PKGrid\Router $router
	 * @param \SS6\ShopBundle\Model\PKGrid\Twig_Environment $twig
	 */
	public function __construct($id, RequestStack $requestStack, Router $router, Twig_Environment $twig) {
		if (empty($id)) {
			$message = 'Grid id can not be empty.';
			throw new \SS6\ShopBundle\Model\PKGrid\Exception\EmptyGridIdException($message);
		}

		$this->id = $id;
		$this->requestStack = $requestStack;
		$this->router = $router;
		$this->twig = $twig;

		$this->limit = $this->defaultLimit;
		$this->page = 1;

		$this->loadFromRequest();
	}

	/**
	 * @param string $id
	 * @param string $queryId
	 * @param string $title
	 * @param boolean $sortable
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
	 * @param string $type
	 * @param string $name
	 * @param string $route
	 * @param array $bindingRouteParams
	 * @param array $additionalRouteParams
	 * @return \SS6\ShopBundle\Model\PKGrid\ActionColumn
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
	 * @param string $inlineEditServiceName
	 * @param string $queryId
	 */
	public function setInlineEditService($inlineEditServiceName, $queryId) {
		$this->inlineEditServiceName = $inlineEditServiceName;
		$this->inlineEditQueryId = $queryId;
	}

	/**
	 * @return string|null
	 */
	public function getInlineEditServiceName() {
		return $this->inlineEditServiceName;
	}

	public function getInlineEditQueryId() {
		return $this->inlineEditQueryId;
	}

	/**
	 * @return bool
	 */
	public function isInlineEdit() {
		return $this->inlineEditServiceName !== null;
	}

	/**
	 * @param array $row
	 * @return mixed
	 */
	public function getInlineEditRowId($row) {
		return PKGrid::getValueFromRowByQueryId($row, $this->getInlineEditQueryId());
	}

	/**
	 * @param string $class
	 */
	public function setActionColumnClass($class) {
		$this->actionColumnClass = $class;
	}

	/**
	 * @return \SS6\ShopBundle\Model\PKGrid\PKGridView
	 */
	public function createView() {
		if ($this->isAllowedPaging()) {
			$this->executeTotalQuery();
		}
		$this->executeQuery();
		$gridView = new PKGridView($this, $this->requestStack, $this->router, $this->twig);

		return $gridView;
	}

	/**
	 * @param string $queryId
	 * @param int $rowId
	 * @return \SS6\ShopBundle\Model\PKGrid\PKGridView
	 */
	public function createViewWithOneRow($queryId, $rowId) {
		$this->executeQueryWithOneRow($queryId, $rowId);
		$gridView = new PKGridView($this, $this->requestStack, $this->router, $this->twig);

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
	public function setDefaultOrder($columnId, $direction = 'asc') {
		if (!$this->isOrderFromRequest) {
			$prefix = $direction == 'desc' ? '-' : '';
			$this->setOrder($prefix . $columnId);
		}
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 */
	public function setQueryBuilder(QueryBuilder $queryBuilder) {
		$this->queryBuilder = $queryBuilder;
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return \SS6\ShopBundle\Model\PKGrid\Column[]
	 */
	public function getColumns() {
		return $this->columns;
	}

	/**
	 * @return \SS6\ShopBundle\Model\PKGrid\ActionColumn[]
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
		if ($this->getOrderDirection() === 'desc') {
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
	public function getActionColumnClass() {
		return $this->actionColumnClass;
	}

	/**
	 * @param string $orderString
	 */
	private function setOrder($orderString) {
		if (substr($orderString, 0, 1) === '-') {
			$this->orderDirection = 'desc';
		} else {
			$this->orderDirection = 'asc';
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

	private function prepareQuery() {
		if ($this->isAllowedPaging()) {
			$this->queryBuilder
				->setFirstResult($this->limit * ($this->page - 1))
				->setMaxResults($this->limit);
		}
		if ($this->order) {
			$this->queryBuilder
				->orderBy($this->columns[$this->order]->getQueryId(), $this->orderDirection);
		}
	}

	/**
	 * @param string $queryId
	 * @param int $rowId
	 */
	private function prepareQueryWithOneRow($queryId, $rowId) {
		$this->queryBuilderWithOneRow = clone $this->queryBuilder;
		$this->queryBuilderWithOneRow
			->andWhere($queryId . ' = :rowId')
			->setParameter('rowId', $rowId)
			->setFirstResult(null)
			->setMaxResults(null)
			->resetDQLPart('orderBy');
	}

	private function prepareTotalQuery() {
		$em = $this->queryBuilder->getEntityManager();

		$totalQueryBuilder = clone $this->queryBuilder;
		$totalQueryBuilder
			->setFirstResult(null)
			->setMaxResults(null)
			->resetDQLPart('orderBy');

		$query = $totalQueryBuilder->getQuery();

		$parametersAssoc = array();
		foreach ($query->getParameters() as $parameter) {
			$parametersAssoc[$parameter->getName()] = $parameter->getValue();
		}

		list($dummyQuery, $flatenedParameters) = SQLParserUtils::expandListParameters(
			$query->getDQL(),
			$parametersAssoc,
			array()
		);

		$sql = 'SELECT COUNT(*) AS total_count FROM (' . $query->getSQL() . ') ORIGINAL_QUERY';

		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('total_count', 'totalCount');
		$this->totalNativeQuery = $em->createNativeQuery($sql, $rsm)
			->setParameters($flatenedParameters);
	}

	private function executeQuery() {
		$this->prepareQuery();
		$this->rows = $this->queryBuilder->getQuery()->execute(null, 'GroupedScalarHydrator');
	}

	/**
	 * @param string $queryId
	 * @param int $rowId
	 */
	private function executeQueryWithOneRow($queryId, $rowId) {
		$this->prepareQueryWithOneRow($queryId, $rowId);
		$this->rows = $this->queryBuilderWithOneRow->getQuery()->execute(null, 'GroupedScalarHydrator');
	}

	private function executeTotalQuery() {
		$this->prepareTotalQuery();
		$this->totalCount = $this->totalNativeQuery->getSingleScalarResult();
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

}
