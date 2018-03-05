<?php

namespace Tests\ShopBundle\Unit\Component\HttpFoundation;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\HttpFoundation\FragmentHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;

class FragmentHandlerTest extends TestCase
{
    public function testRenderNotIgnoreErrorsWithoutDebug()
    {
        $rendererMock = $this->createMock(FragmentRendererInterface::class);
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

        $requestStackMock = $this->createMock(RequestStack::class);
        $requestStackMock->expects($this->any())->method('getCurrentRequest')->will($this->returnValue(Request::create('/')));

        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock->expects($this->once())->method('get')->will($this->returnValue($rendererMock));

        $debug = false;
        $fragmentHandler = new FragmentHandler($containerMock, $requestStackMock, $debug);
        $fragmentHandler->addRendererService('rendererName', 'rendererName');

        $this->expectException(\Exception::class);
        $fragmentHandler->render('uri', 'rendererName', []);
    }

    public function testDeliveryRedirect()
    {
        $response = new Response('', 301);

        $rendererMock = $this->createMock(FragmentRendererInterface::class);
        $rendererMock->expects($this->once())->method('getName')->will($this->returnValue('rendererName'));
        $rendererMock->expects($this->any())->method('render')->willReturn($response);

        $requestStackMock = $this->createMock(RequestStack::class);
        $requestStackMock->expects($this->any())->method('getCurrentRequest')->will($this->returnValue(Request::create('/')));

        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock->expects($this->once())->method('get')->will($this->returnValue($rendererMock));

        $fragmentHandler = new FragmentHandler($containerMock, $requestStackMock, false);
        $fragmentHandler->addRendererService('rendererName', 'rendererName');

        $this->assertSame('', $fragmentHandler->render('uri', 'rendererName', []));
    }

    public function testNotDeliveryErrorResponse()
    {
        $response = new Response('', 500);

        $rendererMock = $this->createMock(FragmentRendererInterface::class);
        $rendererMock->expects($this->once())->method('getName')->will($this->returnValue('rendererName'));
        $rendererMock->expects($this->any())->method('render')->willReturn($response);

        $requestStackMock = $this->createMock(RequestStack::class);
        $requestStackMock->expects($this->any())->method('getCurrentRequest')->will($this->returnValue(Request::create('/')));

        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock->expects($this->once())->method('get')->will($this->returnValue($rendererMock));

        $fragmentHandler = new FragmentHandler($containerMock, $requestStackMock, false);
        $fragmentHandler->addRendererService('rendererName', 'rendererName');

        $this->expectException(\RuntimeException::class);
        $fragmentHandler->render('uri', 'rendererName', []);
    }
}
