<?php

namespace Shopsys\FrameworkBundle\Model\Order;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;

class OrderNumberSequenceRepository
{
    const ID = 1;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

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
    private function getOrderNumberSequenceRepository()
    {
        return $this->em->getRepository(OrderNumberSequence::class);
    }

    /**
     * @return string
     */
    public function getNextNumber()
    {
        try {
            $this->em->beginTransaction();

            $requestedNumber = time();

            $orderNumberSequence = $this->getOrderNumberSequenceRepository()->find(self::ID, LockMode::PESSIMISTIC_WRITE);
            /* @var $orderNumberSequence \Shopsys\FrameworkBundle\Model\Order\OrderNumberSequence|null */
            if ($orderNumberSequence === null) {
                throw new \Shopsys\FrameworkBundle\Model\Order\Exception\OrderNumberSequenceNotFoundException(
                    'Order number sequence ID ' . self::ID . ' not found.'
                );
            }

            $lastNumber = $orderNumberSequence->getNumber();

            if ($requestedNumber <= $lastNumber) {
                $requestedNumber = $lastNumber + 1;
            }

            $orderNumberSequence->setNumber($requestedNumber);

            $this->em->flush($orderNumberSequence);
            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }

        return $requestedNumber;
    }
}
