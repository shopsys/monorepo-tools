<?php

namespace SS6\ShopBundle\Tests\Model\String;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\String\EncodingConvertor;
use stdClass;

class EncodingConvertorTest extends PHPUnit_Framework_TestCase {

	const STRINGUTF8 = 'ěščřžýáíé';

	public function testCp1250ToUtf8() {
		$this->assertEquals(self::STRINGUTF8, EncodingConvertor::cp1250ToUtf8(iconv('UTF-8', 'CP1250', self::STRINGUTF8)));
		$this->assertEquals(
			strtoupper(self::STRINGUTF8),
			EncodingConvertor::cp1250ToUtf8(iconv('UTF-8', 'CP1250', strtoupper(self::STRINGUTF8)))
		);
	}

	public function testCp1250ToUtf8Array() {
		$array = array('key' => self::STRINGUTF8);
		$actual = EncodingConvertor::cp1250ToUtf8(array(
			'key' => iconv('UTF-8', 'CP1250', self::STRINGUTF8))
		);
		$this->assertEquals($array, $actual);
	}

	public function testCp1250ToUtf8ArrayOfArrays() {
		$array = array('key' => self::STRINGUTF8);
		$arrayOfArrays = array('array' => $array);
		$actual = EncodingConvertor::cp1250ToUtf8(array(
			'array' => array('key' => iconv('UTF-8', 'CP1250', self::STRINGUTF8)))
		);
		$this->assertEquals($arrayOfArrays, $actual);
	}

	public function testCp1250ToUtf8Object() {
		$object = new stdClass();
		$this->assertEquals($object, $object);
	}

	public function testCp1250ToUtf8ArrayOfMixed() {
		$array = array('key' => self::STRINGUTF8);
		$object = new stdClass();
		$arrayOfMixed = array('string' => self::STRINGUTF8, 'array' => $array, 'object' => $object);
		$actual = EncodingConvertor::cp1250ToUtf8(array(
			'string' => iconv('UTF-8', 'CP1250', self::STRINGUTF8),
			'array' => array('key' => iconv('UTF-8', 'CP1250', self::STRINGUTF8)),
			'object' => $object)
		);
		$this->assertEquals($arrayOfMixed, $actual);
	}
}
