<?php

namespace Shopsys\FrameworkBundle\Model\Administrator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

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
     * @var \Shopsys\FrameworkBundle\Model\Administrator\AdministratorFactoryInterface
     */
    protected $administratorFactory;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorRepository $administratorRepository
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorFactoryInterface $administratorFactory
     * @param \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $encoderFactory
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage
     */
    public function __construct(
        EntityManagerInterface $em,
        AdministratorRepository $administratorRepository,
        AdministratorFactoryInterface $administratorFactory,
        EncoderFactoryInterface $encoderFactory,
        TokenStorageInterface $tokenStorage
    ) {
        $this->administratorRepository = $administratorRepository;
        $this->em = $em;
        $this->administratorFactory = $administratorFactory;
        $this->encoderFactory = $encoderFactory;
        $this->tokenStorage = $tokenStorage;
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
        $this->setPassword($administrator, $administratorData->password);

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
        $this->checkUsername($administrator, $administratorData->username);
        $administrator->edit($administratorData);
        if ($administratorData->password !== null) {
            $this->setPassword($administrator, $administratorData->password);
        }

        $this->em->flush();

        return $administrator;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\Administrator $administrator
     * @param string $username
     */
    protected function checkUsername(Administrator $administrator, string $username): void
    {
        $administratorByUserName = $this->administratorRepository->findByUserName($username);
        if ($administratorByUserName !== null
            && $administratorByUserName !== $administrator
            && $administratorByUserName->getUsername() === $username
        ) {
            throw new \Shopsys\FrameworkBundle\Model\Administrator\Exception\DuplicateUserNameException($administrator->getUsername());
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\Administrator $administrator
     * @param string $password
     */
    protected function setPassword(Administrator $administrator, string $password): void
    {
        $encoder = $this->encoderFactory->getEncoder($administrator);
        $passwordHash = $encoder->encodePassword($password, $administrator->getSalt());
        $administrator->setPasswordHash($passwordHash);
    }

    /**
     * @param int $administratorId
     */
    public function delete($administratorId)
    {
        $administrator = $this->administratorRepository->getById($administratorId);
        $this->checkForDelete($administrator);
        $this->em->remove($administrator);
        $this->em->flush();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\Administrator $administrator
     */
    protected function checkForDelete(Administrator $administrator)
    {
        $adminCountExcludingSuperadmin = $this->administratorRepository->getCountExcludingSuperadmin();
        if ($adminCountExcludingSuperadmin === 1) {
            throw new \Shopsys\FrameworkBundle\Model\Administrator\Exception\DeletingLastAdministratorException();
        }
        if ($this->tokenStorage->getToken()->getUser() === $administrator) {
            throw new \Shopsys\FrameworkBundle\Model\Administrator\Exception\DeletingSelfException();
        }
        if ($administrator->isSuperadmin()) {
            throw new \Shopsys\FrameworkBundle\Model\Administrator\Exception\DeletingSuperadminException();
        }
    }

    /**
     * @param string $administratorUsername
     * @param string $newPassword
     */
    public function changePassword($administratorUsername, $newPassword)
    {
        $administrator = $this->administratorRepository->getByUserName($administratorUsername);
        $administrator->setPasswordHash($newPassword);
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
