<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use SS6\ShopBundle\Model\PKGrid\InlineEdit\InlineEditInterface;

class InlineEdit implements InlineEditInterface {

	public function getForm() {
		return 'form';
	}
	
}
