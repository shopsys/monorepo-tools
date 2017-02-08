<?php

namespace SS6\ShopBundle\Model\Order\Status;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use SS6\ShopBundle\Model\Localization\AbstractTranslatableEntity;
use SS6\ShopBundle\Model\Order\Status\OrderStatusData;
use SS6\ShopBundle\Model\Order\Status\OrderStatusTranslation;

/**
 * @ORM\Table(name="order_statuses")
 * @ORM\Entity
 */
class OrderStatus extends AbstractTranslatableEntity {

	const TYPE_NEW = 1;
	const TYPE_IN_PROGRESS = 2;
	const TYPE_DONE = 3;
	const TYPE_CANCELED = 4;

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatusTranslation[]
	 *
	 * @Prezent\Translations(targetEntity="SS6\ShopBundle\Model\Order\Status\OrderStatusTranslation")
	 */
	protected $translations;

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 */
	private $type;

	/**
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatusData $orderStatusData
	 * @param int $type
	 */
	public function __construct(OrderStatusData $orderStatusData, $type) {
		$this->translations = new ArrayCollection();
		$this->setType($type);
		$this->setTranslations($orderStatusData);
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string|null $locale
	 * @return string
	 */
	public function getName($locale = null) {
		return $this->translation($locale)->getName();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatusData $orderStatusData
	 */
	private function setTranslations(OrderStatusData $orderStatusData) {
		foreach ($orderStatusData->name as $locale => $name) {
			$this->translation($locale)->setName($name);
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\Status\OrderStatusTranslation
	 */
	protected function createTranslation() {
		return new OrderStatusTranslation();
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
	private function setType($type) {
		if (in_array($type, [
			self::TYPE_NEW,
			self::TYPE_IN_PROGRESS,
			self::TYPE_DONE,
			self::TYPE_CANCELED,
		])) {
			$this->type = $type;
		} else {
			throw new \SS6\ShopBundle\Model\Order\Status\Exception\InvalidOrderStatusTypeException($type);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatusData $orderStatusData
	 */
	public function edit(OrderStatusData $orderStatusData) {
		$this->setTranslations($orderStatusData);
	}

}
