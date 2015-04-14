<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Component\Validator;

use Doctrine\Common\Annotations\AnnotationReader;
use SS6\ShopBundle\Component\Validator\AutoValidatorAnnotationLoader;
use SS6\ShopBundle\Tests\Test\FunctionalTestCase;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class AutoValidatorAnnotationLoaderTest extends FunctionalTestCase {

	public function testLoadClassMetadata() {
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');

		$loader = new AutoValidatorAnnotationLoader(new AnnotationReader(), $em);
		$metadata = new ClassMetadata('SS6\ShopBundle\Tests\Unit\Model\Component\Validator\DataObject');

		$loader->loadClassMetadata($metadata);

		$expected = new ClassMetadata('SS6\ShopBundle\Tests\Unit\Model\Component\Validator\DataObject');
		$expected->addPropertyConstraint('name', new Constraints\NotBlank());
		$expected->addPropertyConstraint('short', new Constraints\Length([
			'max' => 100,
		]));

		// load reflection class so that the comparison passes
		$expected->getReflectionClass();

		$this->assertEquals($expected, $metadata);
	}

}
