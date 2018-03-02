<?php

namespace Tests\ShopBundle\Database\Model\Administrator\Security;

use Shopsys\FrameworkBundle\DataFixtures\Base\AdministratorDataFixture;
use Shopsys\FrameworkBundle\Model\Administrator\Activity\AdministratorActivityFacade;
use Shopsys\FrameworkBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Tests\ShopBundle\Test\DatabaseTestCase;

class AdministratorFrontSecurityFacadeTest extends DatabaseTestCase
{
    public function testIsAdministratorLoggedNot()
    {
        $administratorFrontSecurityFacade = $this->getServiceByType(AdministratorFrontSecurityFacade::class);
        /* @var $administratorFrontSecurityFacade \Shopsys\FrameworkBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade */

        $this->assertFalse($administratorFrontSecurityFacade->isAdministratorLogged());
    }

    public function testIsAdministratorLogged()
    {
        $session = $this->getServiceByType(SessionInterface::class);
        /* @var $session \Symfony\Component\HttpFoundation\Session\SessionInterface */
        $administratorFrontSecurityFacade = $this->getServiceByType(AdministratorFrontSecurityFacade::class);
        /* @var $administratorFrontSecurityFacade \Shopsys\FrameworkBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade */

        $administrator = $this->getReference(AdministratorDataFixture::ADMINISTRATOR);
        /* @var $administrator \Shopsys\FrameworkBundle\Model\Administrator\Administrator */
        $password = '';
        $roles = $administrator->getRoles();
        $token = new UsernamePasswordToken($administrator, $password, AdministratorFrontSecurityFacade::ADMINISTRATION_CONTEXT, $roles);

        $session->set('_security_' . AdministratorFrontSecurityFacade::ADMINISTRATION_CONTEXT, serialize($token));

        $administratorActivityFacade = $this->getServiceByType(AdministratorActivityFacade::class);
        /* @var $administratorActivityFacade \Shopsys\FrameworkBundle\Model\Administrator\Activity\AdministratorActivityFacade */
        $administratorActivityFacade->create($administrator, '127.0.0.1');

        $this->assertTrue($administratorFrontSecurityFacade->isAdministratorLogged());
    }
}
