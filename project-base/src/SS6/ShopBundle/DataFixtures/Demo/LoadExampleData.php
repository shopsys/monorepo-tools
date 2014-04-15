<?php

namespace ND\ShopSys6Bundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
//use ND\ShopSys6Bundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface {

	public function load(ObjectManager $manager) {
		/*$this->createUser($manager, 'Michalangelo', 'Turtle', 'monkey');
		$this->createUser($manager, 'Donatello', 'Turtle', 'secret');
		$this->createUser($manager, 'Raffaello', 'Turtle', 'topsecret');
		$this->createUser($manager, 'Leonardo', 'Turtle', 'hash');

		$manager->flush();*/
	}
	
	/*private function createUser(ObjectManager $manager, $firstname, $lastname, $password) {
		$user = new User();
		$user->setFirstname($firstname);
		$user->setLastname($lastname);
		$user->setPassword($password);
		
		$manager->persist($user);
	}*/

	public function getOrder() {
		return 1;
	}	
}