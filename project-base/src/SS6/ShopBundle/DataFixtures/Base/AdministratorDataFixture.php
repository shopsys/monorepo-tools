<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Administrator\Administrator;
use SS6\ShopBundle\Model\Administrator\AdministratorData;
use SS6\ShopBundle\Model\Administrator\AdministratorService;

class AdministratorDataFixture extends AbstractReferenceFixture {

	const SUPERADMINISTRATOR = 'administrator_superadministrator';
	const ADMINISTRATOR = 'admistrator_administrator';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$administratorService = $this->get(AdministratorService::class);
		/* @var $administratorService \SS6\ShopBundle\Model\Administrator\AdministratorService */

		$superadmin = new Administrator(new AdministratorData(true));
		$superadmin->setUsername('superadmin');
		$superadmin->setRealname('netdevelo s.r.o. - superadmin');
		$superadmin->setPassword($administratorService->getPasswordHash($superadmin, 'admin123'));
		$superadmin->setEmail('no-reply@netdevelo.cz');

		$manager->persist($superadmin);
		$this->addReference(self::SUPERADMINISTRATOR, $superadmin);

		$administrator = new Administrator(new AdministratorData());
		$administrator->setUsername('admin');
		$administrator->setRealname('netdevelo s.r.o.');
		$administrator->setPassword($administratorService->getPasswordHash($administrator, 'admin123'));
		$administrator->setEmail('no-reply@netdevelo.cz');

		$manager->persist($administrator);
		$this->addReference(self::ADMINISTRATOR, $administrator);

		$manager->flush();
	}

}
