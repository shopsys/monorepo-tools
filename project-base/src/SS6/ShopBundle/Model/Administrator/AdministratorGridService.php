<?php

namespace SS6\ShopBundle\Model\Administrator;

use SS6\ShopBundle\Model\Administrator\Administrator;
use SS6\ShopBundle\Model\Administrator\AdministratorGridLimit;

class AdministratorGridService {

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\Administrator $administrator
	 * @param string $gridId
	 * @param int $limit
	 * @return \SS6\ShopBundle\Model\Administrator\AdministratorGridLimit
	 * @throws \SS6\ShopBundle\Model\Administrator\Exception\EmptyGridIdException
	 * @throws \SS6\ShopBundle\Model\Administrator\Exception\InvalidGridLimitValueException
	 */
	public function setGridLimit(Administrator $administrator, $gridId, $limit) {
		if (empty($gridId)) {
			throw new \SS6\ShopBundle\Model\Administrator\Exception\EmptyGridIdException;
		}
		if (!is_int($limit) || $limit <= 0) {
			throw new \SS6\ShopBundle\Model\Administrator\Exception\InvalidGridLimitValueException($limit);
		}

		$gridLimit = $administrator->getGridLimit($gridId);
		if ($gridLimit === null) {
			$gridLimit = new AdministratorGridLimit($administrator, $gridId, $limit);
		} else {
			$gridLimit->setLimit($limit);
		}

		return $gridLimit;
	}
	
}
