<?php

namespace Tests\ShopBundle\Database\Model\Order;

use Shopsys\ShopBundle\Model\Order\OrderNumberSequenceRepository;
use Tests\ShopBundle\Test\DatabaseTestCase;

class OrderNumberSequenceRepositoryTest extends DatabaseTestCase
{
    public function testGetNextNumber()
    {
        $orderNumberSequenceRepository = $this->getServiceByType(OrderNumberSequenceRepository::class);
        /* @var $orderNumberSequenceRepository \Shopsys\ShopBundle\Model\Order\OrderNumberSequenceRepository */

        $numbers = [];
        for ($i = 0; $i < 10; $i++) {
            $numbers[] = $orderNumberSequenceRepository->getNextNumber();
        }

        $uniqueNumbers = array_unique($numbers);

        $this->assertSame(count($numbers), count($uniqueNumbers));
    }
}
