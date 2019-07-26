<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\Model\Administrator\Security;

use Shopsys\FrameworkBundle\Model\Administrator\Activity\AdministratorActivityFacade;
use Shopsys\FrameworkBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade;
use Shopsys\ShopBundle\DataFixtures\Demo\AdministratorDataFixture;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class AdministratorFrontSecurityFacadeTest extends TransactionFunctionalTestCase
{
    public function testIsAdministratorLoggedNot()
    {
        /** @var \Shopsys\FrameworkBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade $administratorFrontSecurityFacade */
        $administratorFrontSecurityFacade = $this->getContainer()->get(AdministratorFrontSecurityFacade::class);

        $this->assertFalse($administratorFrontSecurityFacade->isAdministratorLogged());
    }

    public function testIsAdministratorLogged()
    {
        /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $session */
        $session = $this->getContainer()->get('session');
        /** @var \Shopsys\FrameworkBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade $administratorFrontSecurityFacade */
        $administratorFrontSecurityFacade = $this->getContainer()->get(AdministratorFrontSecurityFacade::class);

        /** @var \Shopsys\ShopBundle\Model\Administrator\Administrator $administrator */
        $administrator = $this->getReference(AdministratorDataFixture::ADMINISTRATOR);
        $password = '';
        $roles = $administrator->getRoles();
        $token = new UsernamePasswordToken($administrator, $password, AdministratorFrontSecurityFacade::ADMINISTRATION_CONTEXT, $roles);

        $session->set('_security_' . AdministratorFrontSecurityFacade::ADMINISTRATION_CONTEXT, serialize($token));

        /** @var \Shopsys\FrameworkBundle\Model\Administrator\Activity\AdministratorActivityFacade $administratorActivityFacade */
        $administratorActivityFacade = $this->getContainer()->get(AdministratorActivityFacade::class);
        $administratorActivityFacade->create($administrator, '127.0.0.1');

        $this->assertTrue($administratorFrontSecurityFacade->isAdministratorLogged());
    }
}
