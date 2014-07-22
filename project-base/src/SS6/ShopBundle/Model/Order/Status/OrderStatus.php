<?php

namespace SS6\ShopBundle\Model\Order\Status;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="order_statuses")
 * @ORM\Entity
 */
class OrderStatus {

	const TYPE_NEW = 1;
	const TYPE_IN_PROGRESS = 2;
	const TYPE_DONE = 3;
	const TYPE_CANCELED = 4;

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
	 * @ORM\Column(type="string", length=100)
	 */
	private $name;

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 */
	private $type;

	/**
	 * @param string $name
	 * @param int $type
	 * @param int|null $id
	 */
	public function __construct($name, $type, $id = null) {
		$this->name = $name;
		$this->setType($type);
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
	 * @return int
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param int $type
	 */
	public function setType($type) {
		if (in_array($type, array(
			self::TYPE_NEW,
			self::TYPE_IN_PROGRESS,
			self::TYPE_DONE,
			self::TYPE_CANCELED,
		))) {
			$this->type = $type;
		} else {
			throw new Exception\InvalidOrderStatusTypeException($type);
		}
	}

	/**
	 * @param string $name
	 */
	public function edit($name) {
		$this->name = $name;
	}
	
}
