<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Department\Department;
use SS6\ShopBundle\Model\Department\DepartmentData;

class DepartmentDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$departmentData = new DepartmentData();
		$departmentData->setNames(array('cs' => 'Televize, audio', 'en' => 'TV, audio'));
		$this->createDepartment($manager, 'department_tv', $departmentData);

		$departmentData->setNames(array('cs' => 'Fotoaparáty', 'en' => 'Cameras & Photo'));
		$this->createDepartment($manager, 'department_photo', $departmentData);

		$departmentData->setNames(array('cs' => 'Tiskárny', 'en' => null));
		$this->createDepartment($manager, 'department_printers', $departmentData);

		$departmentData->setNames(array('cs' => 'Počítače & příslušenství', 'en' => null));
		$this->createDepartment($manager, 'department_pc', $departmentData);

		$departmentData->setNames(array('cs' => 'Mobilní telefony', 'en' => null));
		$this->createDepartment($manager, 'department_phones', $departmentData);

		$departmentData->setNames(array('cs' => 'Kávovary', 'en' => null));
		$this->createDepartment($manager, 'department_coffee', $departmentData);

		$departmentData->setNames(array('cs' => 'Knihy', 'en' => 'Books'));
		$this->createDepartment($manager, 'department_books', $departmentData);

		$departmentData->setNames(array('cs' => 'Hračky a další', 'en' => null));
		$this->createDepartment($manager, 'department_toys', $departmentData);


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
