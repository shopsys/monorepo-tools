<?php

namespace SS6\ShopBundle\Tests\Component\Router;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\Transformers\RemoveDuplicatesFromCollectionTransformer;

class RemoveDuplicatesFromCollectionTransformerTest extends PHPUnit_Framework_TestCase {

	public function testReverseTransform() {
		$collection = new ArrayCollection(['a', 'b', 'a']);

		$transformer = new RemoveDuplicatesFromCollectionTransformer();
		$this->assertSame(['a', 'b'], $transformer->reverseTransform($collection)->toArray());
	}

	public function testReverseTransformPresevesKeys() {
		$collection = new ArrayCollection([0 => 'a', 10 => 'b', 20 => 'a']);

		$transformer = new RemoveDuplicatesFromCollectionTransformer();
		$this->assertSame([0 => 'a', 10 => 'b'], $transformer->reverseTransform($collection)->toArray());
	}

	public function testReverseTransformComparesStrictly() {
		$collection = new ArrayCollection(['0', 0, null, false]);

		$transformer = new RemoveDuplicatesFromCollectionTransformer();
		$this->assertSame(['0', 0, null, false], $transformer->reverseTransform($collection)->toArray());
	}

}
