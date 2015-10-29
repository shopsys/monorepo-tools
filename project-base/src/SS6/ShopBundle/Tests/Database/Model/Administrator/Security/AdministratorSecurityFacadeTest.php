<?php

namespace SS6\ShopBundle\Tests\Database\Model\Administrator\Security;

use SS6\ShopBundle\DataFixtures\Base\AdministratorDataFixture;
use SS6\ShopBundle\Model\Administrator\Activity\AdministratorActivityFacade;
use SS6\ShopBundle\Model\Administrator\Security\AdministratorSecurityFacade;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AdministratorSecurityFacadeTest extends DatabaseTestCase {

	public function testIsAdministratorLoggedNot() {
		$administratorSecurityFacade = $this->getContainer()->get(AdministratorSecurityFacade::class);
		/* @var $administratorSecurityFacade \SS6\ShopBundle\Model\Administrator\Security\AdministratorSecurityFacade */

		$this->assertFalse($administratorSecurityFacade->isAdministratorLogged());
	}

	public function testIsAdministratorLogged() {
		$container = $this->getContainer();
		/* @var $container \Symfony\Component\DependencyInjection\ContainerInterface */
		$session = $container->get(SessionInterface::class);
		/* @var $session \Symfony\Component\HttpFoundation\Session\SessionInterface */
		$administratorSecurityFacade = $container->get(AdministratorSecurityFacade::class);
		/* @var $administratorSecurityFacade \SS6\ShopBundle\Model\Administrator\Security\AdministratorSecurityFacade */

		$administrator = $this->getReference(AdministratorDataFixture::ADMINISTRATOR);
		/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */
		$password = '';
		$roles = $administrator->getRoles();
		$token = new UsernamePasswordToken($administrator, $password, AdministratorSecurityFacade::ADMINISTRATION_CONTEXT, $roles);

		$session->set('_security_' . AdministratorSecurityFacade::ADMINISTRATION_CONTEXT, serialize($token));

		$administratorActivityFacade = $container->get(AdministratorActivityFacade::class);
		/* @var $administratorActivityFacade \SS6\ShopBundle\Model\Administrator\Activity\AdministratorActivityFacade */
		$administratorActivityFacade->create($administrator, '127.0.0.1');

		$this->assertTrue($administratorSecurityFacade->isAdministratorLogged());
	}
}
