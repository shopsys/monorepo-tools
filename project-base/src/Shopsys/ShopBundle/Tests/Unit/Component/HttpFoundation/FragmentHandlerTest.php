<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\HttpFoundation;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\HttpFoundation\FragmentHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;

class FragmentHandlerTest extends PHPUnit_Framework_TestCase
{

    public function testRenderNotIgnoreErrorsWithoutDebug() {
        $rendererMock = $this->getMock(FragmentRendererInterface::class);
        $rendererMock->expects($this->once())->method('getName')->will($this->returnValue('rendererName'));
        $rendererMock->expects($this->atLeastOnce())
            ->method('render')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->callback(function ($options) {
                    return array_key_exists('ignore_errors', $options) && $options['ignore_errors'] === false;
                })
            )
            ->willThrowException(new \Exception());

        $requestStackMock = $this->getMock(RequestStack::class);
        $requestStackMock->expects($this->any())->method('getCurrentRequest')->will($this->returnValue(Request::create('/')));

        $containerMock = $this->getMock(ContainerInterface::class);
        $containerMock->expects($this->once())->method('get')->will($this->returnValue($rendererMock));

        $debug = false;
        $fragmentHandler = new FragmentHandler($containerMock, $debug, $requestStackMock);
        $fragmentHandler->addRendererService('rendererName', 'rendererName');

        $this->setExpectedException(\Exception::class);
        $fragmentHandler->render('uri', 'rendererName', []);
    }

    public function testDeliveryRedirect() {
        $response = new Response('', 301);

        $rendererMock = $this->getMock(FragmentRendererInterface::class);
        $rendererMock->expects($this->once())->method('getName')->will($this->returnValue('rendererName'));
        $rendererMock->expects($this->any())->method('render')->willReturn($response);

        $requestStackMock = $this->getMock(RequestStack::class);
        $requestStackMock->expects($this->any())->method('getCurrentRequest')->will($this->returnValue(Request::create('/')));

        $containerMock = $this->getMock(ContainerInterface::class);
        $containerMock->expects($this->once())->method('get')->will($this->returnValue($rendererMock));

        $fragmentHandler = new FragmentHandler($containerMock, false, $requestStackMock);
        $fragmentHandler->addRendererService('rendererName', 'rendererName');

        $this->assertSame('', $fragmentHandler->render('uri', 'rendererName', []));
    }

    public function testNotDeliveryErrorResponse() {
        $response = new Response('', 500);

        $rendererMock = $this->getMock(FragmentRendererInterface::class);
        $rendererMock->expects($this->once())->method('getName')->will($this->returnValue('rendererName'));
        $rendererMock->expects($this->any())->method('render')->willReturn($response);

        $requestStackMock = $this->getMock(RequestStack::class);
        $requestStackMock->expects($this->any())->method('getCurrentRequest')->will($this->returnValue(Request::create('/')));

        $containerMock = $this->getMock(ContainerInterface::class);
        $containerMock->expects($this->once())->method('get')->will($this->returnValue($rendererMock));

        $fragmentHandler = new FragmentHandler($containerMock, false, $requestStackMock);
        $fragmentHandler->addRendererService('rendererName', 'rendererName');

        $this->setExpectedException(\RuntimeException::class);
        $fragmentHandler->render('uri', 'rendererName', []);
    }
}
