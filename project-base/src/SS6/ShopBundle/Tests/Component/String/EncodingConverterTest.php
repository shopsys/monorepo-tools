<?php

namespace SS6\ShopBundle\Tests\Component\String;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\String\EncodingConverter;
use stdClass;

class EncodingConverterTest extends PHPUnit_Framework_TestCase {

	const STRING_UTF8 = 'příšerně žluťoučký kůň úpěl ďábelské ódy. PŘÍŠERNĚ ŽLUŤOUČKÝ KŮŇ ÚPĚL ĎÁBELSKÉ ÓDY.';

	private function getUtf8String() {
		return self::STRING_UTF8;
	}

	private function getCp1250String() {
		return iconv('UTF-8', 'CP1250', self::STRING_UTF8);
	}

	public function testCp1250ToUtf8() {
		$this->assertEquals($this->getUtf8String(), EncodingConverter::cp1250ToUtf8($this->getCp1250String()));
	}

	public function testCp1250ToUtf8Array() {
		$array = array('key' => $this->getUtf8String());
		$actual = EncodingConverter::cp1250ToUtf8(array(
			'key' => $this->getCp1250String()
		));
		$this->assertEquals($array, $actual);
	}

	public function testCp1250ToUtf8ArrayOfArrays() {
		$array = array('key' => $this->getUtf8String());
		$arrayOfArrays = array('array' => $array);
		$actual = EncodingConverter::cp1250ToUtf8(array(
			'array' => array('key' => $this->getCp1250String())
		));
		$this->assertEquals($arrayOfArrays, $actual);
	}

	public function testCp1250ToUtf8Object() {
		$object = new stdClass();
		$actual = EncodingConverter::cp1250ToUtf8($object);
		$this->assertEquals($object, $actual);
	}

	public function testCp1250ToUtf8ArrayOfMixed() {
		$array = array('key' => $this->getUtf8String());
		$object = new stdClass();
		$arrayOfMixed = array('string' => $this->getUtf8String(), 'array' => $array, 'object' => $object);
		$actual = EncodingConverter::cp1250ToUtf8(array(
			'string' => $this->getCp1250String(),
			'array' => array('key' => $this->getCp1250String()),
			'object' => $object
		));
		$this->assertEquals($arrayOfMixed, $actual);
	}
}
