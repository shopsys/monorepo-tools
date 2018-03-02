<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Model\Order\OrderNumberSequence;
use Shopsys\FrameworkBundle\Model\Order\OrderNumberSequenceRepository;

class OrderNumberSequenceDataFixture extends AbstractReferenceFixture
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $orderNumberSequence = new OrderNumberSequence(OrderNumberSequenceRepository::ID, 0);
        $manager->persist($orderNumberSequence);
        $manager->flush();
    }
}
