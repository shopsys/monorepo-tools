<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\Model\Security;

use Shopsys\FrameworkBundle\Model\Customer\CustomerFacade;
use Shopsys\FrameworkBundle\Model\Security\Authenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Tests\ShopBundle\Test\FunctionalTestCase;

class AuthenticatorTest extends FunctionalTestCase
{
    public function testSessionIdIsChangedAfterLogin(): void
    {
        /** @var \Shopsys\FrameworkBundle\Model\Security\Authenticator $authenticator */
        $authenticator = $this->getContainer()->get(Authenticator::class);
        /** @var \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade $customerFacade */
        $customerFacade = $this->getContainer()->get(CustomerFacade::class);

        $user = $customerFacade->getUserById(1);
        $mockedRequest = $this->createMockedRequest();

        $beforeLoginSessionId = $mockedRequest->getSession()->getId();

        $authenticator->loginUser($user, $mockedRequest);

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
