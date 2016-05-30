<?php

namespace SS6\ShopBundle\Tests\Database\Model\Administrator\Security;

use SS6\ShopBundle\DataFixtures\Base\AdministratorDataFixture;
use SS6\ShopBundle\Model\Administrator\Activity\AdministratorActivityFacade;
use SS6\ShopBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @UglyTest
 */
class AdministratorFrontSecurityFacadeTest extends DatabaseTestCase {

	public function testIsAdministratorLoggedNot() {
		$administratorFrontSecurityFacade = $this->getContainer()->get(AdministratorFrontSecurityFacade::class);
		/* @var $administratorFrontSecurityFacade \SS6\ShopBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade */

		$this->assertFalse($administratorFrontSecurityFacade->isAdministratorLogged());
	}

	public function testIsAdministratorLogged() {
		$container = $this->getContainer();
		/* @var $container \Symfony\Component\DependencyInjection\ContainerInterface */
		$session = $container->get(SessionInterface::class);
		/* @var $session \Symfony\Component\HttpFoundation\Session\SessionInterface */
		$administratorFrontSecurityFacade = $container->get(AdministratorFrontSecurityFacade::class);
		/* @var $administratorFrontSecurityFacade \SS6\ShopBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade */

		$administrator = $this->getReference(AdministratorDataFixture::ADMINISTRATOR);
		/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */
		$password = '';
		$roles = $administrator->getRoles();
		$token = new UsernamePasswordToken($administrator, $password, AdministratorFrontSecurityFacade::ADMINISTRATION_CONTEXT, $roles);

		$session->set('_security_' . AdministratorFrontSecurityFacade::ADMINISTRATION_CONTEXT, serialize($token));

		$administratorActivityFacade = $container->get(AdministratorActivityFacade::class);
		/* @var $administratorActivityFacade \SS6\ShopBundle\Model\Administrator\Activity\AdministratorActivityFacade */
		$administratorActivityFacade->create($administrator, '127.0.0.1');

		$this->assertTrue($administratorFrontSecurityFacade->isAdministratorLogged());
	}
}
