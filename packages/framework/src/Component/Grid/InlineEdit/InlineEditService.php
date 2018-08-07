<?php

namespace Shopsys\FrameworkBundle\Component\Grid\InlineEdit;

use Shopsys\FrameworkBundle\Component\Grid\Grid;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class InlineEditService
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\InlineEdit\GridInlineEditRegistry
     */
    private $gridInlineEditRegistry;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Grid\InlineEdit\GridInlineEditRegistry $gridInlineEditRegistry
     */
    public function __construct(GridInlineEditRegistry $gridInlineEditRegistry)
    {
        $this->gridInlineEditRegistry = $gridInlineEditRegistry;
    }

    /**
     * @param string $serviceName
     * @param mixed $rowId
     * @return string
     */
    public function getRenderedFormRow($serviceName, $rowId)
    {
        $gridInlineEdit = $this->gridInlineEditRegistry->getGridInlineEdit($serviceName);
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
        $gridInlineEdit = $this->gridInlineEditRegistry->getGridInlineEdit($serviceName);
        return $gridInlineEdit->saveForm($request, $rowId);
    }

    /**
     * @param string $serviceName
     * @param mixed $rowId
     * @return string|null
     */
    public function getRenderedRowHtml($serviceName, $rowId)
    {
        $gridInlineEdit = $this->gridInlineEditRegistry->getGridInlineEdit($serviceName);
        $grid = $gridInlineEdit->getGrid();
        /* @var $grid \Shopsys\FrameworkBundle\Component\Grid\Grid */

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
     * @param \Shopsys\FrameworkBundle\Component\Grid\InlineEdit\GridInlineEditInterface $gridInlineEditService
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
     * @param \Shopsys\FrameworkBundle\Component\Grid\Grid $grid
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
