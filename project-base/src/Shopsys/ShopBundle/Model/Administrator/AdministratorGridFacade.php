<?php

namespace Shopsys\ShopBundle\Model\Administrator;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Grid\Grid;
use Shopsys\ShopBundle\Model\Administrator\Administrator;
use Shopsys\ShopBundle\Model\Administrator\AdministratorGridService;

class AdministratorGridFacade
{

    /**
     * @var \Doctrine\ORM\EntityManager;
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Administrator\AdministratorGridService
     */
    private $administratorGridService;

    /**
     * @param \Shopsys\ShopBundle\Model\Administrator\AdministratorGridService $administratorGridService
     */
    public function __construct(EntityManager $em, AdministratorGridService $administratorGridService) {
        $this->em = $em;
        $this->administratorGridService = $administratorGridService;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Administrator\Administrator $administrator
     * @param \Shopsys\ShopBundle\Component\Grid\Grid $grid
     */
    public function restoreAndRememberGridLimit(Administrator $administrator, Grid $grid) {
        $this->administratorGridService->restoreGridLimit($administrator, $grid);
        $gridLimit = $this->administratorGridService->rememberGridLimit($administrator, $grid);
        $this->em->persist($gridLimit);
        $this->em->flush();
    }

}
