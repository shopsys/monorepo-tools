<?php

namespace Shopsys\FrameworkBundle\Model\Payment;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Model\Transport\Transport;

class PaymentRepository
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getPaymentRepository()
    {
        return $this->em->getRepository(Payment::class);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderForAll()
    {
        return $this->getPaymentRepository()->createQueryBuilder('p')
            ->where('p.deleted = :deleted')->setParameter('deleted', false)
            ->orderBy('p.position')
            ->addOrderBy('p.id');
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment[]
     */
    public function getAll()
    {
        return $this->getQueryBuilderForAll()->getQuery()->getResult();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment[]
     */
    public function getAllIncludingDeleted()
    {
        return $this->getPaymentRepository()->findAll();
    }

    /**
     * @param int $id
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment|null
     */
    public function findById($id)
    {
        return $this->getQueryBuilderForAll()
            ->andWhere('p.id = :paymentId')->setParameter('paymentId', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param int $id
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment
     */
    public function getById($id)
    {
        $payment = $this->findById($id);
        if ($payment === null) {
            throw new \Shopsys\FrameworkBundle\Model\Payment\Exception\PaymentNotFoundException(
                'Payment with ID ' . $id . ' not found.'
            );
        }

        return $payment;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment[]
     */
    public function getAllByTransport(Transport $transport)
    {
        return $this->getQueryBuilderForAll()
            ->join('p.transports', 't')
            ->andWhere('t = :transport')->setParameter('transport', $transport)
            ->getQuery()
            ->getResult();
    }
}
