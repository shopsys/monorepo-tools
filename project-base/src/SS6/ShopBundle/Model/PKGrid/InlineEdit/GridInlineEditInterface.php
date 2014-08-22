<?php

namespace SS6\ShopBundle\Model\PKGrid\InlineEdit;

interface GridInlineEditInterface {

	/**
	 * @param mixed $rowId
	 * @return \Symfony\Component\Form\Form
	 */
	public function getForm($rowId);
	
}
