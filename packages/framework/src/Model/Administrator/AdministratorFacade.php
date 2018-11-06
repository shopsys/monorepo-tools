<?php

namespace Shopsys\FrameworkBundle\Model\Administrator;

use Doctrine\ORM\EntityManagerInterface;

class AdministratorFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\AdministratorRepository
     */
    protected $administratorRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\AdministratorService
     */
    protected $administratorService;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\AdministratorFactoryInterface
     */
    protected $administratorFactory;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorRepository $administratorRepository
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorService $administratorService
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorFactoryInterface $administratorFactory
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorFactoryInterface
     */
    public function __construct(
        EntityManagerInterface $em,
        AdministratorRepository $administratorRepository,
        AdministratorService $administratorService,
        AdministratorFactoryInterface $administratorFactory
    ) {
        $this->administratorRepository = $administratorRepository;
        $this->administratorService = $administratorService;
        $this->em = $em;
        $this->administratorFactory = $administratorFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorData $administratorData
     * @return \Shopsys\FrameworkBundle\Model\Administrator\Administrator
     */
    public function create(AdministratorData $administratorData)
    {
        $administratorByUserName = $this->administratorRepository->findByUserName($administratorData->username);
        if ($administratorByUserName !== null) {
            throw new \Shopsys\FrameworkBundle\Model\Administrator\Exception\DuplicateUserNameException($administratorByUserName->getUsername());
        }
        $administrator = $this->administratorFactory->create($administratorData);
        $this->administratorService->setPassword($administrator, $administratorData->password);

        $this->em->persist($administrator);
        $this->em->flush();

        return $administrator;
    }

    /**
     * @param int $administratorId
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorData $administratorData
     * @return \Shopsys\FrameworkBundle\Model\Administrator\Administrator
     */
    public function edit($administratorId, AdministratorData $administratorData)
    {
        $administrator = $this->administratorRepository->getById($administratorId);
        $administratorByUserName = $this->administratorRepository->findByUserName($administratorData->username);
        $administratorEdited = $this->administratorService->edit(
            $administratorData,
            $administrator,
            $administratorByUserName
        );

        $this->em->flush();

        return $administratorEdited;
    }

    /**
     * @param int $administratorId
     */
    public function delete($administratorId)
    {
        $administrator = $this->administratorRepository->getById($administratorId);
        $adminCountExcludingSuperadmin = $this->administratorRepository->getCountExcludingSuperadmin();
        $this->administratorService->delete($administrator, $adminCountExcludingSuperadmin);
        $this->em->remove($administrator);
        $this->em->flush();
    }

    /**
     * @param string $administratorUsername
     * @param string $newPassword
     */
    public function changePassword($administratorUsername, $newPassword)
    {
        $administrator = $this->administratorRepository->getByUserName($administratorUsername);
        $this->administratorService->setPassword($administrator, $newPassword);
        $this->em->flush($administrator);
    }

    /**
     * @param int $administratorId
     * @return \Shopsys\FrameworkBundle\Model\Administrator\Administrator
     */
    public function getById($administratorId)
    {
        return $this->administratorRepository->getById($administratorId);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAllListableQueryBuilder()
    {
        return $this->administratorRepository->getAllListableQueryBuilder();
    }
}
