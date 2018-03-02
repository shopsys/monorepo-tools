<?php

namespace Shopsys\FrameworkBundle\Model\Administrator;

use Doctrine\ORM\EntityManager;
use Shopsys\FrameworkBundle\Component\Grid\Grid;

class AdministratorGridFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager;
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\AdministratorGridService
     */
    private $administratorGridService;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorGridService $administratorGridService
     */
    public function __construct(EntityManager $em, AdministratorGridService $administratorGridService)
    {
        $this->em = $em;
        $this->administratorGridService = $administratorGridService;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\Administrator $administrator
     * @param \Shopsys\FrameworkBundle\Component\Grid\Grid $grid
     */
    public function restoreAndRememberGridLimit(Administrator $administrator, Grid $grid)
    {
        $this->administratorGridService->restoreGridLimit($administrator, $grid);
        $gridLimit = $this->administratorGridService->rememberGridLimit($administrator, $grid);
        $this->em->persist($gridLimit);
        $this->em->flush();
    }
}
