<?php

namespace Shopsys\FrameworkBundle\Component\Grid\InlineEdit;

use Symfony\Component\HttpFoundation\Request;

interface GridInlineEditInterface
{
    /**
     * @param mixed $rowId
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm($rowId);

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param mixed $rowId
     * @return mixed
     */
    public function saveForm(Request $request, $rowId);

    /**
     * @return \Shopsys\FrameworkBundle\Component\Grid\Grid
     */
    public function getGrid();

    /**
     * @return bool
     */
    public function canAddNewRow();

    /**
     * @return string
     */
    public function getServiceName();
}
