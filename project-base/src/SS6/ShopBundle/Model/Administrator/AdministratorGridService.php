<?php

namespace SS6\ShopBundle\Model\Administrator;

use SS6\ShopBundle\Model\Administrator\Administrator;
use SS6\ShopBundle\Model\Administrator\AdministratorGridLimit;
use SS6\ShopBundle\Model\Grid\Grid;

class AdministratorGridService {

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\Administrator $administrator
	 * @param \SS6\ShopBundle\Model\Grid\Grid $grid
	 * @return \SS6\ShopBundle\Model\Administrator\AdministratorGridLimit|null
	 * @throws \SS6\ShopBundle\Model\Administrator\Exception\InvalidGridLimitValueException
	 * @throws \SS6\ShopBundle\Model\Administrator\Exception\RememberGridLimitException
	 */
	public function rememberGridLimit(Administrator $administrator, Grid $grid) {
		if (!$grid->isAllowedPaging()) {
			throw new \SS6\ShopBundle\Model\Administrator\Exception\RememberGridLimitException($grid->getId());
		}
		if ($grid->getLimit() <= 0) {
			throw new \SS6\ShopBundle\Model\Administrator\Exception\InvalidGridLimitValueException($grid->getLimit());
		}

		$gridLimit = $administrator->getGridLimit($grid->getId());
		if ($gridLimit === null) {
			$gridLimit = new AdministratorGridLimit($administrator, $grid->getId(), $grid->getLimit());
		} else {
			$gridLimit->setLimit($grid->getLimit());
		}

		return $gridLimit;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\Administrator $administrator
	 * @param \SS6\ShopBundle\Model\Grid\Grid $grid
	 */
	public function restoreGridLimit(Administrator $administrator, Grid $grid) {
		$customLimit = $administrator->getLimitByGridId($grid->getId());
		if ($customLimit !== null) {
			$grid->setDefaultLimit($customLimit);
		}
	}

}
