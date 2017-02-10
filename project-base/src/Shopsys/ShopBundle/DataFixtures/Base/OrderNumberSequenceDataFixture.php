<?php

namespace Shopsys\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Model\Order\OrderNumberSequence;
use Shopsys\ShopBundle\Model\Order\OrderNumberSequenceRepository;

class OrderNumberSequenceDataFixture extends AbstractReferenceFixture
{

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {
        $orderNumberSequence = new OrderNumberSequence(OrderNumberSequenceRepository::ID, 0);
        $manager->persist($orderNumberSequence);
        $manager->flush();
    }

}
