<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\Administrator\Administrator;
use SS6\ShopBundle\Model\DataFixture\AbstractReferenceFixture;

class AdministratorDataFixture extends AbstractReferenceFixture {
	
	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$administrator = new Administrator();
		
		$encoderFactory = $this->get('security.encoder_factory');
		$encoder = $encoderFactory->getEncoder($administrator);
		$passwordHash = $encoder->encodePassword('admin123', $administrator->getSalt());
	
		$administrator->setUsername('admin');
		$administrator->setRealname('netdevelo s.r.o.');
		$administrator->setPassword($passwordHash);
		$administrator->setLoginToken('');
		
		$manager->persist($administrator);
		$manager->flush();
	}

}
