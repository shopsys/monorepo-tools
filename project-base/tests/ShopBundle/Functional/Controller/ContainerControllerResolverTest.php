<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Tests\ShopBundle\Test\FunctionalTestCase;
use function get_class;

class ContainerControllerResolverTest extends FunctionalTestCase
{
    public function testRedirectControllerObtainableWithResolver(): void
    {
        $containerControllerResolver = new ContainerControllerResolver($this->getContainer());

        $request = Request::create('/');

        $request->attributes->set('_controller', 'Symfony\\Bundle\\FrameworkBundle\\Controller\\RedirectController:redirectAction');

        $controller = $containerControllerResolver->getController($request);

        $this->assertEquals(RedirectController::class, get_class($controller[0]));
    }
}
