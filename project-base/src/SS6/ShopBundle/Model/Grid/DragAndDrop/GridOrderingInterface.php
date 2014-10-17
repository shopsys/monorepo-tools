<?php

namespace SS6\ShopBundle\Model\Grid\DragAndDrop;

interface GridOrderingInterface {

	/**
	 * @param array $rowIds
	 */
	public function saveOrder(array $rowIds);

	/**
	 * @return string
	 */
	public function getServiceName();
	
}
