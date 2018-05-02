<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Model\Order\OrderNumberSequenceFactoryInterface;
use Shopsys\FrameworkBundle\Model\Order\OrderNumberSequenceRepository;

class OrderNumberSequenceDataFixture extends AbstractReferenceFixture
{

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderNumberSequenceFactoryInterface
     */
    protected $orderNumberSequenceFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderNumberSequenceFactoryInterface $orderNumberSequenceFactory
     */
    public function __construct(OrderNumberSequenceFactoryInterface $orderNumberSequenceFactory)
    {
        $this->orderNumberSequenceFactory = $orderNumberSequenceFactory;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $orderNumberSequence = $this->orderNumberSequenceFactory->create(OrderNumberSequenceRepository::ID, '0');
        $manager->persist($orderNumberSequence);
        $manager->flush();
    }
}
