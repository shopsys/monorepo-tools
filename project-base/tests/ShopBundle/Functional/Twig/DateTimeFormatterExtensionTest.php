<?php

namespace Tests\ShopBundle\Functional\Twig;

use DateTime;
use Shopsys\FrameworkBundle\Component\Localization\DateTimeFormatter;
use Shopsys\FrameworkBundle\Model\Localization\Localization;
use Shopsys\FrameworkBundle\Twig\DateTimeFormatterExtension;
use Tests\ShopBundle\Test\FunctionalTestCase;

class DateTimeFormatterExtensionTest extends FunctionalTestCase
{
    public function formatDateDataProvider()
    {
        return [
            ['input' => new DateTime('2015-04-08'), 'locale' => 'cs', 'result' => '8. 4. 2015'],
            ['input' => '2015-04-08', 'locale' => 'cs', 'result' => '8. 4. 2015'],

            ['input' => new DateTime('2015-04-08'), 'locale' => 'en', 'result' => '2015-04-08'],
            ['input' => '2015-04-08', 'locale' => 'en', 'result' => '2015-04-08'],
        ];
    }

    /**
     * @dataProvider formatDateDataProvider
     * @param mixed $input
     * @param mixed $locale
     * @param mixed $result
     */
    public function testFormatDate($input, $locale, $result)
    {
        $localizationMock = $this->getMockBuilder(Localization::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLocale'])
            ->getMock();
        $localizationMock->expects($this->any())->method('getLocale')
            ->willReturn($locale);

        /** @var \Shopsys\FrameworkBundle\Component\Localization\DateTimeFormatter $dateTimeFormatter */
        $dateTimeFormatter = $this->getContainer()->get(DateTimeFormatter::class);
        $dateTimeFormatterExtension = new DateTimeFormatterExtension($dateTimeFormatter, $localizationMock);

        $this->assertSame($result, $dateTimeFormatterExtension->formatDate($input));
    }
}
