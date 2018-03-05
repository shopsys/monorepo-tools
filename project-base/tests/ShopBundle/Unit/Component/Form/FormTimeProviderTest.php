<?php

namespace Tests\ShopBundle\Unit\Component\Form;

use DateTime;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Form\FormTimeProvider;
use Shopsys\FrameworkBundle\Form\TimedFormTypeExtension;
use Symfony\Component\HttpFoundation\Session\Session;

class FormTimeProviderTest extends TestCase
{
    public function isFormTimeValidProvider()
    {
        return [
            [9, '-10 second', true],
            [11, '-10 second', false],
        ];
    }

    /**
     * @dataProvider isFormTimeValidProvider
     * @param int $minimumSeconds
     * @param string $formCreatedAt
     * @param bool $isValid
     */
    public function testIsFormTimeValid($minimumSeconds, $formCreatedAt, $isValid)
    {
        $sessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'has'])
            ->getMock();
        $sessionMock->expects($this->atLeastOnce())->method('get')->will($this->returnValue(new DateTime($formCreatedAt)));
        $sessionMock->expects($this->atLeastOnce())->method('has')->will($this->returnValue(true));

        $formTimeProvider = new FormTimeProvider($sessionMock);

        $options[TimedFormTypeExtension::OPTION_MINIMUM_SECONDS] = $minimumSeconds;
        $this->assertSame($isValid, $formTimeProvider->isFormTimeValid('formName', $options));
    }
}
