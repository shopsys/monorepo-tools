<?php

namespace SS6\CoreBundle\Model\Payment\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use SS6\CoreBundle\Model\Transport\Entity\Transport;

/**
 * @ORM\Table(name="payments")
 * @ORM\Entity
 */
class Payment {

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
	 * @ORM\Column(type="string", length=255)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="decimal", precision=20, scale=6)
	 */
	private $price;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $description;
	
	/**
	 * @var Collection
	 * 
	 * @ORM\ManyToMany(targetEntity="SS6\CoreBundle\Model\Transport\Entity\Transport", inversedBy="payments")
	 * @ORM\JoinTable(name="payments_transports")
	 */
	private $transports;

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $hidden;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $deleted;
	
	/**
	 * @param string $name
	 * @param string $price
	 * @param string|boolean $description
	 * @param boolean $hidden
	 */
	public function __construct($name, $price, Collection $transports = null, $description = null, $hidden = false) {
		$this->name = $name;
		$this->price = $price;
		$this->description = $description;
		$this->transports = $transports instanceof Collection ? $transports : new ArrayCollection();
		$this->hidden = $hidden;
		$this->deleted = false;
	}
	
	/**
	 * @param \SS6\CoreBundle\Model\Transport\Entity\Transport $transport
	 */
	public function addTransport(Transport $transport) {
		if (!$this->transports->contains($transport)) {
			$transport->addPayment($this);
			$this->transports[] = $transport;
		}
	}
	
	/**
	 * @return \SS6\CoreBundle\Model\Transport\Entity\Transport[]
	 */
	public function getTransports() {
		return $this->transports;
	}
	
	/**
	 * @param string $name
	 * @param string $price
	 * @param string|boolean $description
	 * @param boolean $hidden
	 */
	public function setEdit($name, $price, $description, $hidden) {
		$this->name = $name;
		$this->price = $price;
		$this->description = $description;
		$this->hidden = $hidden;
	}

	/**
	 * @return integer 
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
	 * @return string 
	 */
	public function getPrice() {
		return $this->price;
	}

	/**
	 * @return string|null 
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @return boolean
	 */
	public function isHidden() {
		return $this->hidden;
	}
	
	/**
	 * @return boolean
	 */
	public function isDeleted() {
		return $this->deleted;
	}

	/**
	 * @param boolean $deleted
	 */
	public function markAsDeleted() {
		$this->deleted = true;
	}
}
