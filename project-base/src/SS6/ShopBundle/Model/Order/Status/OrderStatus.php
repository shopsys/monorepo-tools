<?php

namespace SS6\ShopBundle\Model\Order\Status;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="order_statuses")
 * @ORM\Entity
 */
class OrderStatus {

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text")
	 */
	private $name;

	/**
	 * @param string $name
	 * @param int $id
	 */
	public function __construct($name, $id = null) {
		$this->name = $name;
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function edit($name) {
		$this->name = $name;
	}
	
}
