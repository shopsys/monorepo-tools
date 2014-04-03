<?php

namespace SS6\CoreBundle\DataFixtures\Base\Administrator;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\CoreBundle\Model\Administrator\Entity\Administrator;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface {
	
	/**
	 * @var ContainerInterface
	 */
	private $container;
	
	/**
	 * {@inheritDoc}
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
		
		$manager->persist($administrator);
		$manager->flush();
	}

	/**
	 * @return int
	 */
	public function getOrder() {
		return 1;
	}	
}