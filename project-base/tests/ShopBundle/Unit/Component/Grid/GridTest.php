<?php

namespace Tests\ShopBundle\Unit\Component\Grid;

use PHPUnit_Framework_TestCase;
use Shopsys\FrameworkBundle\Component\Grid\DataSourceInterface;
use Shopsys\FrameworkBundle\Component\Grid\Grid;
use Shopsys\FrameworkBundle\Component\Grid\GridView;
use Shopsys\FrameworkBundle\Component\Paginator\PaginationResult;
use Shopsys\FrameworkBundle\Component\Router\Security\RouteCsrfProtector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Router;
use Twig_Environment;

class GridTest extends PHPUnit_Framework_TestCase
{
    public function testGetParametersFromRequest()
    {
        $getParameters = [
            Grid::GET_PARAMETER => [
                'gridId' => [
                    'limit' => '100',
                    'page' => '3',
                    'order' => '-name',
                ],
            ],
        ];

        $request = new Request($getParameters);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $twigMock = $this->createMock(Twig_Environment::class);
        $routerMock = $this->createMock(Router::class);
        $routeCsrfProtectorMock = $this->createMock(RouteCsrfProtector::class);
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        $grid = new Grid(
            'gridId',
            $dataSourceMock,
            $requestStack,
            $routerMock,
            $routeCsrfProtectorMock,
            $twigMock
        );

        $this->assertSame('gridId', $grid->getId());
        $this->assertSame(100, $grid->getLimit());
        $this->assertSame(3, $grid->getPage());
        $this->assertSame('name', $grid->getOrderSourceColumnName());
        $this->assertSame('desc', $grid->getOrderDirection());
    }

    public function testAddColumn()
    {
        $request = new Request();
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $twigMock = $this->createMock(Twig_Environment::class);
        $routerMock = $this->createMock(Router::class);
        $routeCsrfProtectorMock = $this->createMock(RouteCsrfProtector::class);
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        $grid = new Grid(
            'gridId',
            $dataSourceMock,
            $requestStack,
            $routerMock,
            $routeCsrfProtectorMock,
            $twigMock
        );
        $grid->addColumn('columnId1', 'sourceColumnName1', 'title1', true)->setClassAttribute('classAttribute');
        $grid->addColumn('columnId2', 'sourceColumnName2', 'title2', false);
        $columns = $grid->getColumnsById();

        $this->assertCount(2, $columns);
        $column2 = array_pop($columns);
        /* @var $column2 \Shopsys\FrameworkBundle\Component\Grid\Column */
        $column1 = array_pop($columns);
        /* @var $column1 \Shopsys\FrameworkBundle\Component\Grid\Column */

        $this->assertSame('columnId1', $column1->getId());
        $this->assertSame('sourceColumnName1', $column1->getSourceColumnName());
        $this->assertSame('title1', $column1->getTitle());
        $this->assertSame(true, $column1->isSortable());
        $this->assertSame('classAttribute', $column1->getClassAttribute());

        $this->assertSame('columnId2', $column2->getId());
        $this->assertSame('sourceColumnName2', $column2->getSourceColumnName());
        $this->assertSame('title2', $column2->getTitle());
        $this->assertSame(false, $column2->isSortable());
        $this->assertSame('', $column2->getClassAttribute());
    }

    public function testAddColumnDuplicateId()
    {
        $request = new Request();
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $twigMock = $this->createMock(Twig_Environment::class);
        $routerMock = $this->createMock(Router::class);
        $routeCsrfProtectorMock = $this->createMock(RouteCsrfProtector::class);
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        $grid = new Grid(
            'gridId',
            $dataSourceMock,
            $requestStack,
            $routerMock,
            $routeCsrfProtectorMock,
            $twigMock
        );
        $grid->addColumn('columnId1', 'sourceColumnName1', 'title1');

        $this->expectException(\Shopsys\FrameworkBundle\Component\Grid\Exception\DuplicateColumnIdException::class);
        $grid->addColumn('columnId1', 'sourceColumnName2', 'title2');
    }

    public function testEnablePaging()
    {
        $request = new Request();
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $twigMock = $this->createMock(Twig_Environment::class);
        $routerMock = $this->createMock(Router::class);
        $routeCsrfProtectorMock = $this->createMock(RouteCsrfProtector::class);
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        $grid = new Grid(
            'gridId',
            $dataSourceMock,
            $requestStack,
            $routerMock,
            $routeCsrfProtectorMock,
            $twigMock
        );
        $grid->enablePaging();
        $this->assertTrue($grid->isEnabledPaging());
    }

    public function testEnablePagingDefaultDisable()
    {
        $request = new Request();
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $twigMock = $this->createMock(Twig_Environment::class);
        $routerMock = $this->createMock(Router::class);
        $routeCsrfProtectorMock = $this->createMock(RouteCsrfProtector::class);
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        $grid = new Grid(
            'gridId',
            $dataSourceMock,
            $requestStack,
            $routerMock,
            $routeCsrfProtectorMock,
            $twigMock
        );
        $this->assertFalse($grid->isEnabledPaging());
    }

    public function testSetDefaultOrder()
    {
        $request = new Request();
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $twigMock = $this->createMock(Twig_Environment::class);
        $routerMock = $this->createMock(Router::class);
        $routeCsrfProtectorMock = $this->createMock(RouteCsrfProtector::class);
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        $grid = new Grid(
            'gridId',
            $dataSourceMock,
            $requestStack,
            $routerMock,
            $routeCsrfProtectorMock,
            $twigMock
        );

        $grid->setDefaultOrder('columnId1', DataSourceInterface::ORDER_DESC);
        $this->assertSame('-columnId1', $grid->getOrderSourceColumnNameWithDirection());

        $grid->setDefaultOrder('columnId2', DataSourceInterface::ORDER_ASC);
        $this->assertSame('columnId2', $grid->getOrderSourceColumnNameWithDirection());
    }

    public function testSetDefaultOrderWithRequest()
    {
        $getParameters = [
            Grid::GET_PARAMETER => [
                'gridId' => [
                    'order' => '-request',
                ],
            ],
        ];

        $request = new Request($getParameters);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $twigMock = $this->createMock(Twig_Environment::class);
        $routerMock = $this->createMock(Router::class);
        $routeCsrfProtectorMock = $this->createMock(RouteCsrfProtector::class);
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        $grid = new Grid(
            'gridId',
            $dataSourceMock,
            $requestStack,
            $routerMock,
            $routeCsrfProtectorMock,
            $twigMock
        );

        $grid->setDefaultOrder('default', DataSourceInterface::ORDER_ASC);
        $this->assertSame('-request', $grid->getOrderSourceColumnNameWithDirection());
    }

    public function testCreateView()
    {
        $request = new Request();
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $twigMock = $this->createMock(Twig_Environment::class);
        $routerMock = $this->createMock(Router::class);
        $routeCsrfProtectorMock = $this->createMock(RouteCsrfProtector::class);
        $dataSourceMock = $this->getMockBuilder(DataSourceInterface::class)
            ->setMethods(['getTotalRowsCount', 'getPaginatedRows'])
            ->getMockForAbstractClass();
        $dataSourceMock->expects($this->never())->method('getTotalRowsCount');
        $dataSourceMock->expects($this->once())->method('getPaginatedRows')
            ->will($this->returnValue(new PaginationResult(1, 1, 0, [])));

        $grid = new Grid(
            'gridId',
            $dataSourceMock,
            $requestStack,
            $routerMock,
            $routeCsrfProtectorMock,
            $twigMock
        );
        $gridView = $grid->createView();

        $this->assertInstanceOf(GridView::class, $gridView);
    }

    public function testCreateViewWithPaging()
    {
        $request = new Request();
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $twigMock = $this->createMock(Twig_Environment::class);
        $routerMock = $this->createMock(Router::class);
        $routeCsrfProtectorMock = $this->createMock(RouteCsrfProtector::class);
        $dataSourceMock = $this->getMockBuilder(DataSourceInterface::class)
            ->setMethods(['getTotalRowsCount', 'getPaginatedRows'])
            ->getMockForAbstractClass();
        $dataSourceMock->expects($this->once())->method('getTotalRowsCount')->will($this->returnValue(0));
        $dataSourceMock->expects($this->once())->method('getPaginatedRows')
            ->will($this->returnValue(new PaginationResult(1, 1, 0, [])));

        $grid = new Grid(
            'gridId',
            $dataSourceMock,
            $requestStack,
            $routerMock,
            $routeCsrfProtectorMock,
            $twigMock
        );
        $grid->enablePaging();
        $gridView = $grid->createView();

        $this->assertInstanceOf(GridView::class, $gridView);
    }

    public function testEnableDragAndDrop()
    {
        $entityClass = 'Path\To\Entity\Class';

        $request = new Request();
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $twigMock = $this->createMock(Twig_Environment::class);
        $routerMock = $this->createMock(Router::class);
        $routeCsrfProtectorMock = $this->createMock(RouteCsrfProtector::class);
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        $grid = new Grid(
            'gridId',
            $dataSourceMock,
            $requestStack,
            $routerMock,
            $routeCsrfProtectorMock,
            $twigMock
        );

        $this->assertFalse($grid->isDragAndDrop());
        $grid->enableDragAndDrop($entityClass);
        $this->assertTrue($grid->isDragAndDrop());
    }
}
