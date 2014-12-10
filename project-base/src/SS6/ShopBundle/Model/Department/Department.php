<?php

namespace SS6\ShopBundle\Model\Department;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use SS6\ShopBundle\Model\Localization\AbstractTranslatableEntity;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="departments")
 * @ORM\Entity
 */
class Department extends AbstractTranslatableEntity {

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @var \SS6\ShopBundle\Model\Department\DepartmentTranslation[]
	 *
	 * @Prezent\Translations(targetEntity="SS6\ShopBundle\Model\Department\DepartmentTranslation")
	 */
	protected $translations;
	
	/**
	 * @var \SS6\ShopBundle\Model\Department\Department
	 *
	 * @Gedmo\TreeParent
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Department\Department", inversedBy="childrens")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $parent;

	/**
	 * @var \SS6\ShopBundle\Model\Department\Department[]
	 *
	 * @ORM\OneToMany(targetEntity="SS6\ShopBundle\Model\Department\Department", mappedBy="parent")
	 * @ORM\OrderBy({"lft" = "ASC"})
	 */
	private $childrens;

	/**
	 * @var int
	 *
	 * @Gedmo\TreeLevel
	 * @ORM\Column(type="integer")
	 */
	private $level;

	/**
	 * @var int
	 *
	 * @Gedmo\TreeLeft
	 * @ORM\Column(type="integer")
	 */
	private $lft;

	/**
	 * @var int
	 *
	 * @Gedmo\TreeRight
	 * @ORM\Column(type="integer")
	 */
	private $rgt;

	/**
	 * @var int|null
	 *
	 * @Gedmo\TreeRoot
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $root;

	/**
	 * @param \SS6\ShopBundle\Model\Department\DepartmentData $departmentData
	 */
	public function __construct(DepartmentData $departmentData) {
		$this->parent = $departmentData->getParent();
		$this->translations = new ArrayCollection();
		$this->setTranslations($departmentData);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Department\DepartmentData $departmentData
	 */
	public function edit(DepartmentData $departmentData) {
		$this->parent = $departmentData->getParent();
		$this->setTranslations($departmentData);
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
	 * @return \SS6\ShopBundle\Model\Department\Department
	 */
	public function getParent() {
		return $this->parent;
	}

	/**
	 * @return int
	 */
	public function getLevel() {
		return $this->level;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Department\Department[]
	 */
	public function getChildrens() {
		return $this->childrens;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Department\DepartmentData $departmentData
	 */
	private function setTranslations(DepartmentData $departmentData) {
		foreach ($departmentData->getNames() as $locale => $name) {
			$this->translation($locale)->setName($name);
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\Department\DepartmentTranslation
	 */
	protected function createTranslation() {
		return new DepartmentTranslation();
	}

}
