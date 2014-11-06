<?php

namespace SS6\ShopBundle\Tests\Model\Component\Validator;

use Doctrine\Common\Annotations\AnnotationReader;
use SS6\ShopBundle\Component\Test\FunctionalTestCase;
use SS6\ShopBundle\Component\Validator\AutoValidatorAnnotationLoader;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class AutoValidatorAnnotationLoaderTest extends FunctionalTestCase {

	public function testLoadClassMetadata() {
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');

		$loader = new AutoValidatorAnnotationLoader(new AnnotationReader(), $em);
		$metadata = new ClassMetadata('SS6\ShopBundle\Tests\Model\Component\Validator\DataObject');

		$loader->loadClassMetadata($metadata);

		$expected = new ClassMetadata('SS6\ShopBundle\Tests\Model\Component\Validator\DataObject');
		$expected->addPropertyConstraint('name', new Constraints\NotBlank());
		$expected->addPropertyConstraint('short', new Constraints\Length(array(
			'max' => 100,
		)));

		// load reflection class so that the comparison passes
		$expected->getReflectionClass();

		$this->assertEquals($expected, $metadata);
	}

}
