<?php

namespace SS6\ShopBundle\Tests\Component\Router;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\Transformers\RemoveWhitespacesTransformer;

class RemoveWhitespacesTransformerTest extends PHPUnit_Framework_TestCase {

	public function transformValuesProvider() {
		return array(
			array('value' => 'foo bar', 'expected' => 'foobar'),
			array('value' => 'FooBar', 'expected' => 'FooBar'),
			array('value' => '  foo  bar  ', 'expected' => 'foobar'),
			array('value' => 'foo	', 'expected' => 'foo'),
			array('value' => "fo\no", 'expected' => 'foo'),
			array('value' => null, 'expected' => null),
		);
	}

	/**
	 * @dataProvider transformValuesProvider
	 */
	public function testReverseTransform($value, $expected) {
		$transformer = new RemoveWhitespacesTransformer();
		$this->assertEquals($expected, $transformer->reverseTransform($value));
	}

}
