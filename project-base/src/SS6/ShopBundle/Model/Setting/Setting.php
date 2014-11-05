<?php

namespace SS6\ShopBundle\Model\Setting;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Setting\SettingValueRepository;

class Setting {

	const ORDER_SUBMITTED_SETTING_NAME = 'order_submitted_text';

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
	 * @param int|null $domainId
	 * @return string|int|float|bool|null
	 */
	public function get($key, $domainId = null) {
		return $this->getSettingValue($key, $domainId)->getValue();
	}

	/**
	 * @param string $key
	 * @param string|int|float|bool|null $value
	 * @param int|null $domainId
	 */
	public function set($key, $value, $domainId = null) {
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
	private function getSettingValue($key, $domainId = null) {
		$this->loadAllData($domainId);

		if (array_key_exists($key, $this->data)) {
			return $this->data[$key];
		}

		$message = 'Setting value with name "' . $key . '" not found.';
		throw new \SS6\ShopBundle\Model\Setting\Exception\SettingValueNotFoundException($message);
	}

	/**
	 * @param int|null $domainId
	 */
	private function loadAllData($domainId = null) {
		if ($this->data === null) {
			$this->data = [];
			if (is_null($domainId)) {
				$settingValues = $this->settingValueRepository->findAll();
			} else {
				$settingValues = $this->settingValueRepository->findAllByDomainId($domainId);
			}

			foreach ($settingValues as $settingValue) {
				$this->data[$settingValue->getName()] = $settingValue;
			}
		}
	}

}
