<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\Administrator\Administrator;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AdministratorData extends AbstractFixture implements ContainerAwareInterface {
	
	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	private $container;
	
	/**
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
	 */
	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$administrator = new Administrator();
		
		$encoderFactory = $this->container->get('security.encoder_factory');
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
