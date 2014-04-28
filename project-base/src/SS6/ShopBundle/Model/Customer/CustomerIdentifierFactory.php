<?php

namespace SS6\ShopBundle\Model\Customer;

use SS6\ShopBundle\Model\Customer\CustomerIdentifier;

class CustomerIdentifierFactory {
	
	/**
	 * @return \SS6\ShopBundle\Model\Customer\CustomerIdentifier
	 */
	public function get() {
		$customerIdentifier = new CustomerIdentifier(session_id());
		
		return $customerIdentifier;
	}
}
