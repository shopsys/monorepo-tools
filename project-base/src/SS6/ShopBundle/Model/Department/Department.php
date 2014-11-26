<?php

namespace SS6\ShopBundle\Model\Department;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use SS6\ShopBundle\Model\Localization\AbstractTranslatableEntity;

/**
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
	 * @param \SS6\ShopBundle\Model\Department\DepartmentData $departmentData
	 */
	public function __construct(DepartmentData $departmentData) {
		$this->translations = new ArrayCollection();
		$this->setTranslations($departmentData);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Department\DepartmentData $departmentData
	 */
	public function edit(DepartmentData $departmentData) {
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
