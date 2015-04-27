<?php

namespace SS6\ShopBundle\Tests\Twig;

use DateTime;
use SS6\ShopBundle\Component\Localization\DateTimeFormatter;
use SS6\ShopBundle\Model\Localization\Localization;
use SS6\ShopBundle\Tests\Test\FunctionalTestCase;
use SS6\ShopBundle\Twig\DateTimeFormatterExtension;

class DateTimeFormatterExtensionTest extends FunctionalTestCase {

	public function formatDateDataProvider() {
		return [
			['input' => new DateTime('2015-04-08'), 'locale' => 'cs', 'result' => '8. 4. 2015'],
			['input' => '2015-04-08', 'locale' => 'cs', 'result' => '8. 4. 2015'],

			['input' => new DateTime('2015-04-08'), 'locale' => 'en', 'result' => '2015-04-08'],
			['input' => '2015-04-08', 'locale' => 'en', 'result' => '2015-04-08'],
		];
	}

	/**
	 * @dataProvider formatDateDataProvider
	 */
	public function testFormatDate($input, $locale, $result) {
		$localizationMock = $this->getMockBuilder(Localization::class)
			->disableOriginalConstructor()
			->setMethods(['getLocale'])
			->getMock();
		$localizationMock->expects($this->any())->method('getLocale')
			->willReturn($locale);

		$dateTimeFormatter = $this->getContainer()->get(DateTimeFormatter::class);
		/* @var $dateTimeFormatter \SS6\ShopBundle\Component\Localization\DateTimeFormatter */

		$dateTimeFormatterExtension = new DateTimeFormatterExtension($dateTimeFormatter, $localizationMock);

		$this->assertSame($result, $dateTimeFormatterExtension->formatDate($input));
	}
}
