<?php

namespace Shopsys\ShopBundle\Model\Transport;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;

class TransportRepository
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
    private function getTransportRepository()
    {
        return $this->em->getRepository(Transport::class);
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getTransportDomainRepository()
    {
        return $this->em->getRepository(TransportDomain::class);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderForAll()
    {
        return $this->getTransportRepository()->createQueryBuilder('t')
            ->where('t.deleted = :deleted')->setParameter('deleted', false)
            ->orderBy('t.position')
            ->addOrderBy('t.id');
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Transport\Transport[]
     */
    public function getAll()
    {
        return $this->getQueryBuilderForAll()->getQuery()->getResult();
    }

    /**
     * @param array $transportIds
     * @return \Shopsys\ShopBundle\Model\Transport\Transport[]
     */
    public function getAllByIds(array $transportIds)
    {
        if (count($transportIds) === 0) {
            return [];
        }

        return $this->getQueryBuilderForAll()
            ->andWhere('t.id IN (:transportIds)')->setParameter('transportIds', $transportIds)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Transport\Transport[]
     */
    public function getAllByDomainId($domainId)
    {
        return $this->getQueryBuilderForAll()
            ->join(TransportDomain::class, 'td', Join::WITH, 't.id = td.transport AND td.domainId = :domainId')
            ->setParameter('domainId', $domainId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Transport\Transport[]
     */
    public function getAllIncludingDeleted()
    {
        return $this->getTransportRepository()->findAll();
    }

    /**
     * @param int $id
     * @return \Shopsys\ShopBundle\Model\Transport\Transport|null
     */
    public function findById($id)
    {
        return $this->getQueryBuilderForAll()
            ->andWhere('t.id = :transportId')->setParameter('transportId', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param int $id
     * @return \Shopsys\ShopBundle\Model\Transport\Transport
     */
    public function getById($id)
    {
        $transport = $this->findById($id);
        if ($transport === null) {
            throw new \Shopsys\ShopBundle\Model\Transport\Exception\TransportNotFoundException(
                'Transport with ID ' . $id . ' not found.'
            );
        }

        return $transport;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Transport\Transport $transport
     * @return \Shopsys\ShopBundle\Model\Transport\TransportDomain[]
     */
    public function getTransportDomainsByTransport(Transport $transport)
    {
        return $this->getTransportDomainRepository()->findBy(['transport' => $transport]);
    }
}
