<?php

namespace SS6\ShopBundle\Model\PKGrid\InlineEdit;

use Symfony\Component\HttpFoundation\Request;

interface GridInlineEditInterface {

	/**
	 * @param mixed $rowId
	 * @return \Symfony\Component\Form\Form
	 */
	public function getForm($rowId);

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param mixed $rowId
	 * @return mixed
	 * @throws \SS6\ShopBundle\Model\PKGrid\InlineEdit\Exception\InvalidFormDataException
	 */
	public function saveForm(Request $request, $rowId);

	/**
	 * @return \SS6\ShopBundle\Model\PKGrid\PKGrid
	 */
	public function getGrid();

	/**
	 * @return string
	 */
	public function getServiceName();

	/**
	 * @return string
	 */
	public function getQueryId();
	
}
