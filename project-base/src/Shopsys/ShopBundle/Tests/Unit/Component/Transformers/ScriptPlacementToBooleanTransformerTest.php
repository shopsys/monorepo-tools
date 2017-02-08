<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\Transformers;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Transformers\ScriptPlacementToBooleanTransformer;
use Shopsys\ShopBundle\Model\Script\Script;
use stdClass;

class ScriptPlacementToBooleanTransformerTest extends PHPUnit_Framework_TestCase {

	public function testTransform() {
		$allPagesPlacement = Script::PLACEMENT_ALL_PAGES;
		$orderPagePlacement = Script::PLACEMENT_ORDER_SENT_PAGE;
		$transformer = new ScriptPlacementToBooleanTransformer();

		$this->assertFalse($transformer->transform($allPagesPlacement));
		$this->assertTrue($transformer->transform($orderPagePlacement));
	}

	/**
	 * @dataProvider reverseTransformDataProvider
	 */
	public function testReverseTransform($scriptHasOrderPlacement, $scriptPlacement) {
		$transformer = new ScriptPlacementToBooleanTransformer();

		$this->assertSame($scriptPlacement, $transformer->reverseTransform($scriptHasOrderPlacement));
	}

	/**
	 * @dataProvider reverseTransformExceptionDataProvider
	 */
	public function testReverseTransformException($param) {
		$transformer = new ScriptPlacementToBooleanTransformer();

		$this->setExpectedException(\Symfony\Component\Form\Exception\TransformationFailedException::class);
		$transformer->reverseTransform($param);
	}

	public function reverseTransformDataProvider() {
		return [
			[true, Script::PLACEMENT_ORDER_SENT_PAGE],
			[false, Script::PLACEMENT_ALL_PAGES],
		];
	}

	public function reverseTransformExceptionDataProvider() {
		return [
			['string'],
			[456],
			[[]],
			[new stdClass()],
		];
	}

}
