<?php

namespace Tests\ShopBundle\Functional\Model\Security;

use Shopsys\FrameworkBundle\Model\Customer\CustomerFacade;
use Shopsys\FrameworkBundle\Model\Security\LoginService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Tests\ShopBundle\Test\FunctionalTestCase;

class LoginServiceTest extends FunctionalTestCase
{
    public function testSessionIdIsChangedAfterLogin(): void
    {
        /** @var \Shopsys\FrameworkBundle\Model\Security\LoginService $loginService */
        $loginService = $this->getContainer()->get(LoginService::class);
        /** @var \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade $customerFacade */
        $customerFacade = $this->getContainer()->get(CustomerFacade::class);

        $user = $customerFacade->getUserById(1);
        $mockedRequest = $this->createMockedRequest();

        $beforeLoginSessionId = $mockedRequest->getSession()->getId();

        $loginService->loginUser($user, $mockedRequest);

        $afterLoginSessionId = $mockedRequest->getSession()->getId();

        $this->assertNotSame($beforeLoginSessionId, $afterLoginSessionId);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    private function createMockedRequest(): Request
    {
        $request = new Request();

        $session = new Session(new MockArraySessionStorage());
        $session->setId('abc');

        $request->setSession($session);

        return $request;
    }
}
