<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\Administrator\Administrator;
use SS6\ShopBundle\Model\Administrator\AdministratorData;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;

class AdministratorDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$administrator = new Administrator(new AdministratorData());

		$administratorService = $this->get('ss6.shop.administrator.administrator_service');
		/* @var $administratorService \SS6\ShopBundle\Model\Administrator\AdministratorService */
		$administrator->setUsername('admin');
		$administrator->setRealname('netdevelo s.r.o.');
		$administrator->setPassword($administratorService->getPasswordHash($administrator, 'admin123'));
		$administrator->setEmail('no-reply@netdevelo.cz');

		$manager->persist($administrator);
		$manager->flush();
	}

}
