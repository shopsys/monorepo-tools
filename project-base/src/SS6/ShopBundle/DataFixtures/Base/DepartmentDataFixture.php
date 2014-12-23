<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Department\Department;
use SS6\ShopBundle\Model\Department\DepartmentData;

class DepartmentDataFixture extends AbstractReferenceFixture {

	const ELECTRONICS = 'department_electronics';
	const TV = 'department_tv';
	const PHOTO = 'department_photo';
	const PRINTERS = 'department_printers';
	const PC = 'department_pc';
	const PHONES = 'department_phones';
	const COFFEE = 'department_coffee';
	const BOOKS = 'department_books';
	const TOYS = 'department_toys';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$departmentData = new DepartmentData();

		$departmentData->setName(array('cs' => 'Elektro', 'en' => 'Electronics'));
		$electronicsDepartment = $this->createDepartment($manager, self::ELECTRONICS, $departmentData);

		$departmentData->setName(array('cs' => 'Televize, audio', 'en' => 'TV, audio'));
		$departmentData->setParent($electronicsDepartment);
		$this->createDepartment($manager, self::TV, $departmentData);

		$departmentData->setName(array('cs' => 'Fotoaparáty', 'en' => 'Cameras & Photo'));
		$this->createDepartment($manager, self::PHOTO, $departmentData);

		$departmentData->setName(array('cs' => 'Tiskárny', 'en' => null));
		$this->createDepartment($manager, self::PRINTERS, $departmentData);

		$departmentData->setName(array('cs' => 'Počítače & příslušenství', 'en' => null));
		$this->createDepartment($manager, self::PC, $departmentData);

		$departmentData->setName(array('cs' => 'Mobilní telefony', 'en' => null));
		$this->createDepartment($manager, self::PHONES, $departmentData);

		$departmentData->setName(array('cs' => 'Kávovary', 'en' => null));
		$this->createDepartment($manager, self::COFFEE, $departmentData);

		$departmentData->setName(array('cs' => 'Knihy', 'en' => 'Books'));
		$departmentData->setParent(null);
		$this->createDepartment($manager, self::BOOKS, $departmentData);

		$departmentData->setName(array('cs' => 'Hračky a další', 'en' => null));
		$this->createDepartment($manager, self::TOYS, $departmentData);


		$manager->flush();
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param string $referenceName
	 * @param \SS6\ShopBundle\Model\Department\DepartmentData $departmentData
	 */
	public function createDepartment(ObjectManager $manager, $referenceName, DepartmentData $departmentData) {
		$department = new Department($departmentData);
		$manager->persist($department);
		$this->addReference($referenceName, $department);

		return $department;
	}

}
