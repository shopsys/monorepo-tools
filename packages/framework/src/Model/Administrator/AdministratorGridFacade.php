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
        $administrator->rememberGridLimit($grid, $this->administratorGridLimitFactory);
        $this->em->flush();
    }
}
