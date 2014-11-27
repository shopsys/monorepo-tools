<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Department\Department;
use SS6\ShopBundle\Model\Department\DepartmentData;

class DepartmentDataFixture extends AbstractReferenceFixture {

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
		$departmentData->setNames(array('cs' => 'Televize, audio', 'en' => 'TV, audio'));
		$this->createDepartment($manager, self::TV, $departmentData);

		$departmentData->setNames(array('cs' => 'Fotoaparáty', 'en' => 'Cameras & Photo'));
		$this->createDepartment($manager, self::PHOTO, $departmentData);

		$departmentData->setNames(array('cs' => 'Tiskárny', 'en' => null));
		$this->createDepartment($manager, self::PRINTERS, $departmentData);

		$departmentData->setNames(array('cs' => 'Počítače & příslušenství', 'en' => null));
		$this->createDepartment($manager, self::PC, $departmentData);

		$departmentData->setNames(array('cs' => 'Mobilní telefony', 'en' => null));
		$this->createDepartment($manager, self::PHONES, $departmentData);

		$departmentData->setNames(array('cs' => 'Kávovary', 'en' => null));
		$this->createDepartment($manager, self::COFFEE, $departmentData);

		$departmentData->setNames(array('cs' => 'Knihy', 'en' => 'Books'));
		$this->createDepartment($manager, self::BOOKS, $departmentData);

		$departmentData->setNames(array('cs' => 'Hračky a další', 'en' => null));
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
	}

}
