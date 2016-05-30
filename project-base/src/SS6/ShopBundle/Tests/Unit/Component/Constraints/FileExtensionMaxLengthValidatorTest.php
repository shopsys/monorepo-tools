<?php

namespace SS6\ShopBundle\Tests\Unit\Component\Cron;

use SS6\ShopBundle\Component\Constraints\FileExtensionMaxLength;
use SS6\ShopBundle\Component\Constraints\FileExtensionMaxLengthValidator;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;

/**
 * @UglyTest
 */
class FileExtensionMaxLengthValidatorTest extends AbstractConstraintValidatorTest {

	/**
	 * @inheritdoc
	 */
	protected function createValidator() {
		return new FileExtensionMaxLengthValidator();
	}

	public function testValidateValidLength() {
		$file = new File(__DIR__ . '/' . 'non-existent.file', false);

		$constraint = new FileExtensionMaxLength([
			'limit' => 4,
			'message' => 'myMessage',
		]);

		$this->validator->validate($file, $constraint);
		$this->assertNoViolation();
	}

	public function testValidateInvalidLength() {
		$file = new File(__DIR__ . '/' . 'non-existent.file', false);

		$constraint = new FileExtensionMaxLength([
			'limit' => 3,
			'message' => 'myMessage',
		]);

		$this->validator->validate($file, $constraint);

		$this->buildViolation('myMessage')
			->setParameter('{{ value }}', '"' . $file->getExtension() . '"')
			->setParameter('{{ limit }}', 3)
			->assertRaised();
	}

}
