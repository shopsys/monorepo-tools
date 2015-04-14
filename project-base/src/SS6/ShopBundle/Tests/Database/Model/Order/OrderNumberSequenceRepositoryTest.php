<?php

namespace SS6\ShopBundle\Tests\Database\Model\Order;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;

class OrderNumberSequenceRepositoryTest extends DatabaseTestCase {

	public function testGetNextNumber() {
		$orderNumberSequenceRepository = $this->getContainer()->get('ss6.shop.order.order_number_sequence_repository');
		/* @var $orderNumberSequenceRepository \SS6\ShopBundle\Model\Order\OrderNumberSequenceRepository */

		$numbers = [];
		for ($i = 0; $i < 10; $i++) {
			$numbers[] = $orderNumberSequenceRepository->getNextNumber();
		}

		$uniqueNumbers = array_unique($numbers);

		$this->assertSame(count($numbers), count($uniqueNumbers));
	}

}
