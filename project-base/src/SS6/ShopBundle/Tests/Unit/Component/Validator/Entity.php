<?php

namespace SS6\ShopBundle\Tests\Unit\Component\Validator;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Entity {

	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @ORM\Column(type="text", length=200)
	 */
	private $name;

	/**
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $short;

}
