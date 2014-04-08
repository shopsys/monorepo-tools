<?php

namespace SS6\CoreBundle\Model\Security\Tests\Listener;

use DateTime;
use PHPUnit_Framework_TestCase;
use SS6\CoreBundle\Model\Product\Entity\Product;

class ProductTest extends PHPUnit_Framework_TestCase {
	
	public function testIsVisibleWhenHidden() {
		$product = new Product();
		$product->setHidden(true);
		
		$this->assertFalse($product->isVisible());
	}
	
	public function testIsVisibleWhenNotHidden() {
		$product = new Product();
		$product->setHidden(false);
		
		$this->assertTrue($product->isVisible());
	}
	
	public function testIsVisibleWhenSellingInFuture() {
		$sellingFrom = new DateTime('now');
		$sellingFrom->modify('+1 day');
		
		$product = new Product();
		$product->setHidden(false);
		$product->setSellingFrom($sellingFrom);
		
		$this->assertFalse($product->isVisible());
	}
	
	public function testIsVisibleWhenSellingInPast() {
		$sellingTo = new DateTime('now');
		$sellingTo->modify('-1 day');
		
		$product = new Product();
		$product->setHidden(false);
		$product->setSellingTo($sellingTo);
		
		$this->assertFalse($product->isVisible());
	}
	
	public function testIsVisibleWhenSellingNow() {
		$sellingFrom = new DateTime('now');
		$sellingFrom->modify('-1 day');
		$sellingTo = new DateTime('now');
		$sellingTo->modify('+1 day');
		
		$product = new Product();
		$product->setHidden(false);
		$product->setSellingFrom($sellingFrom);
		$product->setSellingTo($sellingTo);
		
		$this->assertTrue($product->isVisible());
	}
}
