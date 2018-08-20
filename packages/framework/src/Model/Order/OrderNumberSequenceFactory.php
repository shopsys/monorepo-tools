<?php

namespace Shopsys\FrameworkBundle\Model\Order;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;

class OrderNumberSequenceFactory implements OrderNumberSequenceFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    private $entityNameResolver;

    /**
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     */
    public function __construct(EntityNameResolver $entityNameResolver)
    {
        $this->entityNameResolver = $entityNameResolver;
    }

    /**
     * @param int $id
     * @param string $number
     * @return \Shopsys\FrameworkBundle\Model\Order\OrderNumberSequence
     */
    public function create(int $id, string $number): OrderNumberSequence
    {
        $classData = $this->entityNameResolver->resolve(OrderNumberSequence::class);

        return new $classData($id, $number);
    }
}
