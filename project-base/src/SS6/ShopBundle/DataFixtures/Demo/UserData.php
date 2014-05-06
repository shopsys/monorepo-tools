<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\Customer\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface {
	
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
		$user = new User('John', 'Watson', 'no-reply@netdevelo.cz');
		
		$encoderFactory = $this->container->get('security.encoder_factory');
		$encoder = $encoderFactory->getEncoder($user);
		$passwordHash = $encoder->encodePassword('user123', $user->getSalt());
		
		$user->changePassword($passwordHash);
				
		$manager->persist($user);
		$manager->flush();
	}

	/**
	 * @return int
	 */
	public function getOrder() {
		return 1;
	}	
}
