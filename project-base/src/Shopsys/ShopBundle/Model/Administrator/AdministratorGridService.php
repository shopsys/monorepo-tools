<?php

namespace Shopsys\ShopBundle\Model\Administrator;

use Shopsys\ShopBundle\Component\Grid\Grid;

class AdministratorGridService
{
    /**
     * @param \Shopsys\ShopBundle\Model\Administrator\Administrator $administrator
     * @param \Shopsys\ShopBundle\Component\Grid\Grid $grid
     * @return \Shopsys\ShopBundle\Model\Administrator\AdministratorGridLimit|null
     */
    public function rememberGridLimit(Administrator $administrator, Grid $grid)
    {
        if (!$grid->isEnabledPaging()) {
            throw new \Shopsys\ShopBundle\Model\Administrator\Exception\RememberGridLimitException($grid->getId());
        }
        if ($grid->getLimit() <= 0) {
            throw new \Shopsys\ShopBundle\Model\Administrator\Exception\InvalidGridLimitValueException($grid->getLimit());
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
     * @param \Shopsys\ShopBundle\Model\Administrator\Administrator $administrator
     * @param \Shopsys\ShopBundle\Component\Grid\Grid $grid
     */
    public function restoreGridLimit(Administrator $administrator, Grid $grid)
    {
        $customLimit = $administrator->getLimitByGridId($grid->getId());
        if ($customLimit !== null) {
            $grid->setDefaultLimit($customLimit);
        }
    }
}
