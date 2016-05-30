<?php

namespace SS6\ShopBundle\Tests\Database\Model\Order;

use DateTime;
use SS6\ShopBundle\DataFixtures\Base\AdministratorDataFixture;
use SS6\ShopBundle\Model\Administrator\AdministratorRepository;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

/**
 * @UglyTest
 */
class AdministratorRepositoryTest extends DatabaseTestCase {

	public function testGetByValidMultidomainLogin() {
		$validMultidomainLoginToken = 'validMultidomainLoginToken';
		$multidomainLoginTokenExpiration = new DateTime('+60 seconds');

		$administrator = $this->getReference(AdministratorDataFixture::ADMINISTRATOR);
		/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */
		$administratorRepository = $this->getContainer()->get(AdministratorRepository::class);
		/* @var $administratorRepository \SS6\ShopBundle\Model\Administrator\AdministratorRepository */

		$administrator->setMultidomainLoginTokenWithExpiration($validMultidomainLoginToken, $multidomainLoginTokenExpiration);
		$this->getEntityManager()->flush($administrator);

		$administratorFromDb = $administratorRepository->getByValidMultidomainLoginToken($validMultidomainLoginToken);

		$this->assertSame($administrator, $administratorFromDb);
	}

	public function testGetByValidMultidomainLoginTokenInvalidTokenException() {
		$validMultidomainLoginToken = 'validMultidomainLoginToken';
		$invalidMultidomainLoginToken = 'invalidMultidomainLoginToken';
		$multidomainLoginTokenExpiration = new DateTime('+60 seconds');

		$administrator = $this->getReference(AdministratorDataFixture::ADMINISTRATOR);
		/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */
		$administratorRepository = $this->getContainer()->get(AdministratorRepository::class);
		/* @var $administratorRepository \SS6\ShopBundle\Model\Administrator\AdministratorRepository */

		$administrator->setMultidomainLoginTokenWithExpiration($validMultidomainLoginToken, $multidomainLoginTokenExpiration);
		$this->getEntityManager()->flush($administrator);

		$this->setExpectedException('\SS6\ShopBundle\Model\Administrator\Security\Exception\InvalidTokenException');

		$administratorRepository->getByValidMultidomainLoginToken($invalidMultidomainLoginToken);
	}

	public function testGetByValidMultidomainLoginTokenExpiredTokenException() {
		$validMultidomainLoginToken = 'validMultidomainLoginToken';
		$multidomainLoginTokenExpiration = new DateTime('-60 seconds');

		$administrator = $this->getReference(AdministratorDataFixture::ADMINISTRATOR);
		/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */
		$administratorRepository = $this->getContainer()->get(AdministratorRepository::class);
		/* @var $administratorRepository \SS6\ShopBundle\Model\Administrator\AdministratorRepository */

		$administrator->setMultidomainLoginTokenWithExpiration($validMultidomainLoginToken, $multidomainLoginTokenExpiration);
		$this->getEntityManager()->flush($administrator);

		$this->setExpectedException('\SS6\ShopBundle\Model\Administrator\Security\Exception\InvalidTokenException');

		$administratorRepository->getByValidMultidomainLoginToken($validMultidomainLoginToken);
	}

}
