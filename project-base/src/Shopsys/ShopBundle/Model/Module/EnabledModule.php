<?php

namespace Shopsys\ShopBundle\Model\Module;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="enabled_modules")
 * @ORM\Entity
 */
class EnabledModule {

	/**
	 * @var string
	 *
	 * @ORM\Id
	 * @ORM\Column(type="string", length=100)
	 */
	private $name;

	/**
	 * @param string $name
	 */
	public function __construct($name) {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

}
