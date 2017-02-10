<?php

namespace Shopsys\ShopBundle\Model\Administrator;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Administrator\AdministratorData;
use Shopsys\ShopBundle\Model\Administrator\AdministratorRepository;
use Shopsys\ShopBundle\Model\Administrator\AdministratorService;

class AdministratorFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Administrator\AdministratorRepository
     */
    private $administratorRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Administrator\AdministratorService
     */
    private $administratorService;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Shopsys\ShopBundle\Model\Administrator\AdministratorRepository $administratorRepository
     * @param \Shopsys\ShopBundle\Model\Administrator\AdministratorService $administratorService
     */
    public function __construct(
        EntityManager $em,
        AdministratorRepository $administratorRepository,
        AdministratorService $administratorService
    ) {
        $this->administratorRepository = $administratorRepository;
        $this->administratorService = $administratorService;
        $this->em = $em;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Administrator\AdministratorData $administratorData
     * @return \Shopsys\ShopBundle\Model\Administrator\Administrator
     */
    public function create(AdministratorData $administratorData) {
        if (in_array($administratorData->username, $this->getSuperadminUsernames())) {
            throw new \Shopsys\ShopBundle\Model\Administrator\Exception\DuplicateSuperadminNameException($administratorData->username);
        }
        $administratorByUserName = $this->administratorRepository->findByUserName($administratorData->username);
        if ($administratorByUserName !== null) {
            throw new \Shopsys\ShopBundle\Model\Administrator\Exception\DuplicateUserNameException($administratorByUserName->getUsername());
        }
        $administrator = new Administrator($administratorData);
        $administrator->setPassword($this->administratorService->getPasswordHash($administrator, $administratorData->password));

        $this->em->persist($administrator);
        $this->em->flush();

        return $administrator;

    }

    /**
     * @param int $administratorId
     * @param \Shopsys\ShopBundle\Model\Administrator\AdministratorData $administratorData
     * @return \Shopsys\ShopBundle\Model\Administrator\Administrator
     */
    public function edit($administratorId, AdministratorData $administratorData) {
        $administrator = $this->administratorRepository->getById($administratorId);
        $administratorByUserName = $this->administratorRepository->findByUserName($administratorData->username);
        $superadminUsernames = $this->getSuperadminUsernames();
        $administratorEdited = $this->administratorService->edit(
            $administratorData,
            $administrator,
            $superadminUsernames,
            $administratorByUserName
        );

        $this->em->flush();

        return $administratorEdited;
    }

    /**
     * @param int $administratorId
     */
    public function delete($administratorId) {
        $administrator = $this->administratorRepository->getById($administratorId);
        $adminCountExcludingSuperadmin = $this->administratorRepository->getCountExcludingSuperadmin();
        $this->administratorService->delete($administrator, $adminCountExcludingSuperadmin);
        $this->em->remove($administrator);
        $this->em->flush();
    }

    /**
     * @param int $administratorId
     * @return \Shopsys\ShopBundle\Model\Administrator\Administrator
     */
    public function getById($administratorId) {
        return $this->administratorRepository->getById($administratorId);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAllListableQueryBuilder() {
        return $this->administratorRepository->getAllListableQueryBuilder();
    }

    /**
     * @return string[]
     */
    public function getSuperadminUsernames() {
        $superadmins = $this->administratorRepository->getAllSuperadmins();
        $superadminUsernames = [];
        foreach ($superadmins as $superadmin) {
            $superadminUsernames[] = $superadmin->getUsername();
        }

        return $superadminUsernames;
    }
}
