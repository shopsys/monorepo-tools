<?php

namespace Tests\ShopBundle\Database\Model\Administrator\Security;

use Shopsys\ShopBundle\DataFixtures\Base\AdministratorDataFixture;
use Shopsys\ShopBundle\Model\Administrator\Activity\AdministratorActivityFacade;
use Shopsys\ShopBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Tests\ShopBundle\Test\DatabaseTestCase;

class AdministratorFrontSecurityFacadeTest extends DatabaseTestCase
{
    public function testIsAdministratorLoggedNot()
    {
        $administratorFrontSecurityFacade = $this->getContainer()->get(AdministratorFrontSecurityFacade::class);
        /* @var $administratorFrontSecurityFacade \Shopsys\ShopBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade */

        $this->assertFalse($administratorFrontSecurityFacade->isAdministratorLogged());
    }

    public function testIsAdministratorLogged()
    {
        $container = $this->getContainer();
        /* @var $container \Symfony\Component\DependencyInjection\ContainerInterface */
        $session = $container->get(SessionInterface::class);
        /* @var $session \Symfony\Component\HttpFoundation\Session\SessionInterface */
        $administratorFrontSecurityFacade = $container->get(AdministratorFrontSecurityFacade::class);
        /* @var $administratorFrontSecurityFacade \Shopsys\ShopBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade */

        $administrator = $this->getReference(AdministratorDataFixture::ADMINISTRATOR);
        /* @var $administrator \Shopsys\ShopBundle\Model\Administrator\Administrator */
        $password = '';
        $roles = $administrator->getRoles();
        $token = new UsernamePasswordToken($administrator, $password, AdministratorFrontSecurityFacade::ADMINISTRATION_CONTEXT, $roles);

        $session->set('_security_' . AdministratorFrontSecurityFacade::ADMINISTRATION_CONTEXT, serialize($token));

        $administratorActivityFacade = $container->get(AdministratorActivityFacade::class);
        /* @var $administratorActivityFacade \Shopsys\ShopBundle\Model\Administrator\Activity\AdministratorActivityFacade */
        $administratorActivityFacade->create($administrator, '127.0.0.1');

        $this->assertTrue($administratorFrontSecurityFacade->isAdministratorLogged());
    }
}
