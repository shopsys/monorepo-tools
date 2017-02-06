<?php

namespace SS6\ShopBundle\Tests\Database\Model\Order;

use SS6\ShopBundle\Model\Order\OrderNumberSequenceRepository;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

class OrderNumberSequenceRepositoryTest extends DatabaseTestCase {

	public function testGetNextNumber() {
		$orderNumberSequenceRepository = $this->getContainer()->get(OrderNumberSequenceRepository::class);
		/* @var $orderNumberSequenceRepository \SS6\ShopBundle\Model\Order\OrderNumberSequenceRepository */

		$numbers = [];
		for ($i = 0; $i < 10; $i++) {
			$numbers[] = $orderNumberSequenceRepository->getNextNumber();
		}

		$uniqueNumbers = array_unique($numbers);

		$this->assertSame(count($numbers), count($uniqueNumbers));
	}

}
