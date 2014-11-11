<?php

namespace SS6\ShopBundle\Model\Setting;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Setting\SettingValueRepository;

class Setting {

	const ORDER_SUBMITTED_SETTING_NAME = 'order_submitted_text';
	const COMMON_VALUE = 0;
	const DEFAULT_VALUE = 'default';

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
	 * @param string $key
	 * @param int $domainId
	 * @return string|int|float|bool|null
	 */
	public function get($key, $domainId = self::COMMON_VALUE) {
		return $this->getSettingValue($key, $domainId)->getValue();
	}

	/**
	 * @param string $key
	 * @param string|int|float|bool|null $value
	 * @param int $domainId
	 */
	public function set($key, $value, $domainId = self::COMMON_VALUE) {
		if ($domainId === null) {
			throw new \SS6\ShopBundle\Model\Setting\Exception\InvalidArgumentException("Domain id can not be null");
		}

		$settingValue = $this->getSettingValue($key, $domainId);
		$settingValue->edit($value);
		$this->em->flush($settingValue);
	}

	/**
	 * @param string $key
	 * @param int|nul $domainId
	 * @return \SS6\ShopBundle\Model\Setting\SettingValue
	 * @throws \SS6\ShopBundle\Model\Setting\Exception\SettingValueNotFoundException
	 */
	private function getSettingValue($key, $domainId) {
		$domainId = (int)$domainId;
		$this->loadAllData($domainId);

		if ($domainId !== self::COMMON_VALUE) {
			$priority = array($domainId, self::COMMON_VALUE, self::DEFAULT_VALUE);
		} else {
			$priority = array(self::COMMON_VALUE, self::DEFAULT_VALUE);
		}

		foreach ($priority as $valueType) {
			if (isset($this->data[$valueType]) && array_key_exists($key, $this->data[$valueType])) {
				return $this->data[$valueType][$key];
			}
		}

		$message = 'Setting value with name "' . $key . '" not found.';
		throw new \SS6\ShopBundle\Model\Setting\Exception\SettingValueNotFoundException($message);
	}

	/**
	 * @param int|null $domainId
	 */
	private function loadAllData($domainId) {
		if ($this->data === null) {
			$this->data = [];
			$settingValuesForAllDomains = $this->settingValueRepository->findAllForAllDomains();
			$this->fillData($settingValuesForAllDomains, self::COMMON_VALUE);

			$settingValuesDefault = $this->settingValueRepository->findAllDefault();
			$this->fillData($settingValuesDefault, self::DEFAULT_VALUE);

			if ($domainId > 0) {
				$settingValuesDomain = $this->settingValueRepository->findAllByDomainId($domainId);
				$this->fillData($settingValuesDomain, $domainId);
			}
		}
	}

	/**
	 * @param array $settingData
	 * @param int|string $index
	 */
	private function fillData(array $settingData, $index) {
		foreach ($settingData as $value) {
			$this->data[$index][$value->getName()] = $value;
		}
	}

}
