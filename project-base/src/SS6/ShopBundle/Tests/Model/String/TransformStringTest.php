<?php

namespace SS6\ShopBundle\Tests\Model\String;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\String\TransformString;

class TransformStringTest extends PHPUnit_Framework_TestCase {

	public function testSafeFilename() {
		$this->assertEquals('escrzyaie.dat', TransformString::safeFilename('ěščřžýáíé.dat'));
		$this->assertEquals('ESCRZYAIE.DAT', TransformString::safeFilename('ĚŠČŘŽÝÁÍÉ.DAT'));
		$this->assertEquals('Foo_Bar.dat', TransformString::safeFilename('Foo     Bar.dat'));
		$this->assertEquals('Foo-Bar.dat', TransformString::safeFilename('Foo-Bar.dat'));
		$this->assertEquals('_._Foo.dat', TransformString::safeFilename('../../Foo.dat'));
		$this->assertEquals('_._Foo.dat', TransformString::safeFilename('..\\..\\Foo.dat'));
		$this->assertEquals('foo.dat', TransformString::safeFilename('.foo.dat'));
	}

}
