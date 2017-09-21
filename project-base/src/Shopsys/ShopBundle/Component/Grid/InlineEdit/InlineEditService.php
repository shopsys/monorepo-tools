<?php

namespace Shopsys\ShopBundle\Component\Grid\InlineEdit;

use Shopsys\ShopBundle\Component\Grid\Grid;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class InlineEditService
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(
        ContainerInterface $container
    ) {
        $this->container = $container;
    }

    /**
     * @param string $serviceName
     * @param mixed $rowId
     * @return string
     */
    public function getRenderedFormRow($serviceName, $rowId)
    {
        $gridInlineEdit = $this->getInlineEditService($serviceName);
        $form = $gridInlineEdit->getForm($rowId);

        return $this->renderFormAsRow($gridInlineEdit, $rowId, $form);
    }

    /**
     * @param string $serviceName
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param mixed $rowId
     * @return mixed
     */
    public function saveFormData($serviceName, Request $request, $rowId)
    {
        $gridInlineEdit = $this->getInlineEditService($serviceName);
        return $gridInlineEdit->saveForm($request, $rowId);
    }

    /**
     * @param string $serviceName
     * @param mixed $rowId
     */
    public function getRenderedRowHtml($serviceName, $rowId)
    {
        $gridInlineEdit = $this->getInlineEditService($serviceName);
        $grid = $gridInlineEdit->getGrid();
        /* @var $grid \Shopsys\ShopBundle\Component\Grid\Grid */

        $gridView = $grid->createViewWithOneRow($rowId);
        $rows = $grid->getRows();
        $rowData = array_pop($rows);
        return $gridView->renderBlock('grid_row', [
            'loopIndex' => 0,
            'lastRow' => false,
            'row' => $rowData,
        ], false);
    }

    /**
     * @param string $serviceName
     * @return \Shopsys\ShopBundle\Component\Grid\InlineEdit\GridInlineEditInterface
     */
    private function getInlineEditService($serviceName)
    {
        $gridInlineEdit = $this->container->get($serviceName, ContainerInterface::NULL_ON_INVALID_REFERENCE);

        if ($gridInlineEdit instanceof GridInlineEditInterface) {
            return $gridInlineEdit;
        } else {
            throw new \Shopsys\ShopBundle\Component\Grid\InlineEdit\Exception\InvalidServiceException($serviceName);
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Grid\InlineEdit\GridInlineEditInterface $gridInlineEditService
     * @param mixed $rowId
     * @param \Symfony\Component\Form\Form $form
     * @return string
     */
    private function renderFormAsRow(GridInlineEditInterface $gridInlineEditService, $rowId, Form $form)
    {
        $grid = $gridInlineEditService->getGrid();
        if ($rowId === null) {
            $gridView = $grid->createViewWithoutRows();
        } else {
            $gridView = $grid->createViewWithOneRow($rowId);
        }

        return $gridView->renderBlock('grid_row', $this->getFormRowTemplateParameters($grid, $form), false);
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Grid\Grid $grid
     * @param \Symfony\Component\Form\Form $form
     * @return array
     */
    private function getFormRowTemplateParameters(Grid $grid, Form $form)
    {
        $formView = $form->createView();
        $rows = $grid->getRows();

        return [
            'loopIndex' => 0,
            'lastRow' => false,
            'row' => array_pop($rows),
            'form' => $formView,
        ];
    }
}
