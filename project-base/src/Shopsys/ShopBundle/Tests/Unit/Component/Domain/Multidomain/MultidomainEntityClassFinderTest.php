<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\Domain\Multidomain;

use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Domain\Multidomain\MultidomainEntityClassFinder;

class MultidomainEntityClassFinderTest extends PHPUnit_Framework_TestCase {

	public function testGetMultidomainEntitiesNames() {
		$classMetadataMock1 = $this->getMock(ClassMetadata::class, [], [], '', false);
		$classMetadataMock1
			->method('getIdentifierFieldNames')
			->willReturn(['id', 'testId']);
		$classMetadataMock1
			->method('getName')
			->willReturn('NonMultidomainClass1');

		$classMetadataMock2 = $this->getMock(ClassMetadata::class, [], [], '', false);
		$classMetadataMock2
			->method('getIdentifierFieldNames')
			->willReturn(['domainId']);
		$classMetadataMock2
			->method('getName')
			->willReturn('NonMultidomainClass2');

		$classMetadataMock3 = $this->getMock(ClassMetadata::class, [], [], '', false);
		$classMetadataMock3
			->method('getIdentifierFieldNames')
			->willReturn(['id', 'domainId']);
		$classMetadataMock3
			->method('getName')
			->willReturn('MultidomainClass');
		$allClassesMetadata = [$classMetadataMock1, $classMetadataMock2, $classMetadataMock3];

		$multidomainEntityClassFinder = new MultidomainEntityClassFinder();
		$multidomainEntitiesNames = $multidomainEntityClassFinder->getMultidomainEntitiesNames($allClassesMetadata, [], []);

		$this->assertSame(['MultidomainClass'], $multidomainEntitiesNames);
	}

}
