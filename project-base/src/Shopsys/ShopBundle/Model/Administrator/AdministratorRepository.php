<?php

namespace Shopsys\ShopBundle\Model\Administrator;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Administrator\Administrator;

class AdministratorRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getAdministratorRepository()
    {
        return $this->em->getRepository(Administrator::class);
    }

    /**
     * @param int $administratorId
     * @return \Shopsys\ShopBundle\Model\Administrator\Administrator|null
     */
    public function findById($administratorId)
    {
        return $this->getAdministratorRepository()->find($administratorId);
    }

    /**
     * @param int $administratorId
     * @return \Shopsys\ShopBundle\Model\Administrator\Administrator
     */
    public function getById($administratorId)
    {
        $administrator = $this->getAdministratorRepository()->find($administratorId);
        if ($administrator === null) {
            $message = 'Administrator with ID ' . $administratorId . ' not found.';
            throw new \Shopsys\ShopBundle\Model\Administrator\Exception\AdministratorNotFoundException($message);
        }

        return $administrator;
    }

    /**
     * @param string $multidomainLoginToken
     * @return \Shopsys\ShopBundle\Model\Administrator\Administrator
     */
    public function getByValidMultidomainLoginToken($multidomainLoginToken)
    {
        $queryBuilder = $this->getAdministratorRepository()
            ->createQueryBuilder('a')
            ->where('a.multidomainLoginToken = :multidomainLoginToken')
            ->setParameter('multidomainLoginToken', $multidomainLoginToken)
            ->andWhere('a.multidomainLoginTokenExpiration > CURRENT_TIMESTAMP()');
        $administrator = $queryBuilder->getQuery()->getOneOrNullResult();
        if ($administrator === null) {
            $message = 'Administrator with valid multidomain login token ' . $multidomainLoginToken . ' not found.';
            throw new \Shopsys\ShopBundle\Model\Administrator\Security\Exception\InvalidTokenException($message);
        }

        return $administrator;
    }

    /**
     * @param string $administratorUserName
     * @return \Shopsys\ShopBundle\Model\Administrator\Administrator|null
     */
    public function findByUserName($administratorUserName)
    {
        return $this->getAdministratorRepository()->findOneBy(['username' => $administratorUserName]);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAllListableQueryBuilder()
    {
        return $this->getAdministratorRepository()
            ->createQueryBuilder('a')
            ->where('a.superadmin = :isSuperadmin')
            ->setParameter('isSuperadmin', false);
    }

    /**
     * @return int
     */
    public function getCountExcludingSuperadmin()
    {
        return (int)($this->getAllListableQueryBuilder()
            ->select('COUNT(a)')
            ->getQuery()->getSingleScalarResult());
    }

    /**
     * @param int $id
     * @param string $loginToken
     * @return \Shopsys\ShopBundle\Model\Administrator\Administrator|null
     */
    public function findByIdAndLoginToken($id, $loginToken)
    {
        return $this->getAdministratorRepository()->findOneBy([
            'id' => $id,
            'loginToken' => $loginToken,
        ]);
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Administrator\Administrator[]
     */
    public function getAllSuperadmins()
    {
        return $this->getAdministratorRepository()->findBy(['superadmin' => true]);
    }
}
