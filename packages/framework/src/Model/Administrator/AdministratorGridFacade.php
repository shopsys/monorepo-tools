<?php

namespace Shopsys\FrameworkBundle\Model\Administrator;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Grid\Grid;

class AdministratorGridFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface;
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\AdministratorGridLimitFactoryInterface
     */
    protected $administratorGridLimitFactory;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorGridLimitFactoryInterface $administratorGridLimitFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        AdministratorGridLimitFactoryInterface $administratorGridLimitFactory
    ) {
        $this->em = $em;
        $this->administratorGridLimitFactory = $administratorGridLimitFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\Administrator $administrator
     * @param \Shopsys\FrameworkBundle\Component\Grid\Grid $grid
     */
    public function restoreAndRememberGridLimit(Administrator $administrator, Grid $grid)
    {
        $administrator->restoreGridLimit($grid);
        $this->rememberGridLimit($administrator, $grid);
        $this->em->flush();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\Administrator $administrator
     * @param \Shopsys\FrameworkBundle\Component\Grid\Grid $grid
     */
    protected function rememberGridLimit(Administrator $administrator, Grid $grid)
    {
        if (!$grid->isEnabledPaging()) {
            throw new \Shopsys\FrameworkBundle\Model\Administrator\Exception\RememberGridLimitException($grid->getId());
        }
        if ($grid->getLimit() <= 0) {
            throw new \Shopsys\FrameworkBundle\Model\Administrator\Exception\InvalidGridLimitValueException($grid->getLimit());
        }

        $gridLimit = $administrator->getGridLimit($grid->getId());
        if ($gridLimit === null) {
            $gridLimit = $this->administratorGridLimitFactory->create($administrator, $grid->getId(), $grid->getLimit());
            $administrator->addGridLimit($gridLimit);
        } else {
            $gridLimit->setLimit($grid->getLimit());
        }
    }
}
