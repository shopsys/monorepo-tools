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
	 */
	public function saveForm(Request $request, $rowId);
	
}
