<?php

namespace SS6\ShopBundle\Model\Setting;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Setting\SettingValueRepository;

class Setting3 {

	const INPUT_PRICE_TYPE = 'inputPriceType';

	const INPUT_PRICE_TYPE_WITH_VAT = 1;
	const INPUT_PRICE_TYPE_WITHOUT_VAT = 2;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Setting\SettingValueRepository
	 */
	private $settingValueRepository;

	/**
	 *
	 * @var \SS6\ShopBundle\Model\Setting\SettingValue[]
	 */
	private $data;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Setting\SettingValueRepository $settingValueRepository
	 */
	public function __construct(EntityManager $em, SettingValueRepository $settingValueRepository) {
		$this->em = $em;
		$this->settingValueRepository = $settingValueRepository;
	}

	/**
	 * @param type $key
	 * @return string|int|float|bool|null
	 */
	public function get($key) {
		return $this->getSettingValue($key)->getValue();
	}

	/**
	 * @param string $key
	 * @param string|int|float|bool|null $value
	 */
	public function set($key, $value) {
		$settingValue = $this->getSettingValue($key);
		$settingValue->edit($value);
		$this->em->flush($value);
	}

	/**
	 * @param string $key
	 * @return \SS6\ShopBundle\Model\Setting\SettingValue
	 * @throws \SS6\ShopBundle\Model\Setting\Exception\SettingValueNotFoundException
	 */
	private function getSettingValue($key) {
		$this->loadAllData();

		if (array_key_exists($key, $this->data)) {
			return $this->data[$key];
		}

		$message = 'Setting value with name "' . $key . '" not found.';
		throw new \SS6\ShopBundle\Model\Setting\Exception\SettingValueNotFoundException($message);
	}

	private function loadAllData() {
		if ($this->data === null) {
			$this->data = [];
			$settingValues = $this->settingValueRepository->findAll();
			foreach ($settingValues as $settingValue) {
				$this->data[$settingValue->getName()] = $settingValue;
			}
		}
	}

}
