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
	 * @var \SS6\ShopBundle\Model\Setting\SettingValue[]
	 */
	private $values = array();

	/**
	 * @var \SS6\ShopBundle\Model\Setting\SettingValue[]
	 */
	private $valuesDefault;

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
	public function get($key, $domainId) {
		return $this->getSettingValue($key, $domainId)->getValue();
	}

	/**
	 * @param string $key
	 * @param string|int|float|bool|null $value
	 * @param int $domainId
	 */
	public function set($key, $value, $domainId) {
		if ($domainId === SettingValue::DOMAIN_ID_DEFAULT) {
			throw new \SS6\ShopBundle\Model\Setting\Exception\InvalidArgumentException('Cannot set default setting value');
		}

		$settingValue = $this->getSettingValue($key, $domainId);
		if ($settingValue->getDomainId() === $domainId) {
			$settingValue->edit($value);
		} else {
			$settingValue = new SettingValue($key, $value, $domainId);
			$this->em->persist($settingValue);
			$this->values[$domainId][$key] = $settingValue;
		}

		$this->em->flush($settingValue);
	}

	/**
	 * @param string $key
	 * @param int|null $domainId
	 * @return \SS6\ShopBundle\Model\Setting\SettingValue
	 * @throws \SS6\ShopBundle\Model\Setting\Exception\SettingValueNotFoundException
	 */
	private function getSettingValue($key, $domainId) {
		$this->loadValues($domainId);

		if ($domainId !== SettingValue::DOMAIN_ID_COMMON && $domainId !== SettingValue::DOMAIN_ID_DEFAULT) {
			if (array_key_exists($key, $this->values[$domainId])) {
				return $this->values[$domainId][$key];
			}
		}

		if (array_key_exists($key, $this->values[SettingValue::DOMAIN_ID_COMMON])) {
			return $this->values[SettingValue::DOMAIN_ID_COMMON][$key];
		}

		if (array_key_exists($key, $this->valuesDefault)) {
			return $this->valuesDefault[$key];
		}

		$message = 'Setting value with name "' . $key . '" not found.';
		throw new \SS6\ShopBundle\Model\Setting\Exception\SettingValueNotFoundException($message);
	}

	private function loadValues($domainId) {
		if ($domainId !== SettingValue::DOMAIN_ID_COMMON && $domainId !== SettingValue::DOMAIN_ID_DEFAULT) {
			$this->loadDomainValues($domainId);
		}

		$this->loadDomainValues(SettingValue::DOMAIN_ID_COMMON);
		$this->loadDefaultValues();
	}

	private function loadDomainValues($domainId) {
		if (!array_key_exists($domainId, $this->values)) {
			$this->values[$domainId] = array();
			foreach ($this->settingValueRepository->findAllByDomainId($domainId) as $settingValue) {
				/* @var $settingValue SettingValue */
				$this->values[$domainId][$settingValue->getName()] = $settingValue;
			}
		}
	}

	private function loadDefaultValues() {
		if ($this->valuesDefault === null) {
			$this->valuesDefault = [];
			foreach ($this->settingValueRepository->findAllDefault() as $settingValue) {
				/* @var $settingValue SettingValue */
				$this->valuesDefault[$settingValue->getName()] = $settingValue;
			}
		}
	}

}
