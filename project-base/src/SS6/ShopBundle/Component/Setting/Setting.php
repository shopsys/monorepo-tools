<?php

namespace SS6\ShopBundle\Component\Setting;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use SS6\ShopBundle\Component\Setting\SettingValueRepository;

class Setting {

	const ORDER_SUBMITTED_SETTING_NAME = 'orderSubmittedText';
	const DEFAULT_PRICING_GROUP = 'defaultPricingGroupId';
	const DEFAULT_AVAILABILITY_IN_STOCK = 'defaultAvailabilityInStockId';
	const TERMS_AND_CONDITIONS_ARTICLE_ID = 'termsAndConditionsArticleId';
	const DOMAIN_DATA_CREATED = 'domainDataCreated';
	const FEED_HASH = 'feedHash';
	const DEFAULT_UNIT = 'defaultUnitId';
	const BASE_URL = 'baseUrl';
	const FEED_NAME_TO_CONTINUE = 'feedNameToContinue';
	const FEED_DOMAIN_ID_TO_CONTINUE = 'feedDomainIdToContinue';
	const FEED_ITEM_ID_TO_CONTINUE = 'feedItemIdToContinue';

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Component\Setting\SettingValueRepository
	 */
	private $settingValueRepository;

	/**
	 * @var \SS6\ShopBundle\Component\Setting\SettingValue[]
	 */
	private $values;

	/**
	 * @var \SS6\ShopBundle\Component\Setting\SettingValue[]
	 */
	private $valuesDefault;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Component\Setting\SettingValueRepository $settingValueRepository
	 */
	public function __construct(EntityManager $em, SettingValueRepository $settingValueRepository) {
		$this->em = $em;
		$this->settingValueRepository = $settingValueRepository;
		$this->clearCache();
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
			throw new \SS6\ShopBundle\Component\Setting\Exception\InvalidArgumentException('Cannot set default setting value');
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
	 * @param int $fromDomainId
	 * @param int $toDomainId
	 */
	public function copyAllMultidomainSettings($fromDomainId, $toDomainId) {
		$query = $this->em->createNativeQuery('
			INSERT INTO setting_values (name, value, type, domain_id)
			SELECT name, value, type, :toDomainId
			FROM setting_values
			WHERE domain_id = :fromDomainId
				AND EXISTS (
					SELECT 1
					FROM setting_values
					WHERE domain_id IS NOT NULL
						AND domain_id != :commonDomainId
				)
			',
			new ResultSetMapping()
		);
		$query->execute([
			'toDomainId' => $toDomainId,
			'fromDomainId' => $fromDomainId,
			'commonDomainId' => SettingValue::DOMAIN_ID_COMMON,
		]);

		$this->clearCache();
	}

	/**
	 * @param string $key
	 * @param int|null $domainId
	 * @return \SS6\ShopBundle\Component\Setting\SettingValue
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
		throw new \SS6\ShopBundle\Component\Setting\Exception\SettingValueNotFoundException($message);
	}

	/**
	 * @param int|null $domainId
	 */
	private function loadValues($domainId) {
		if ($domainId !== SettingValue::DOMAIN_ID_COMMON && $domainId !== SettingValue::DOMAIN_ID_DEFAULT) {
			$this->loadDomainValues($domainId);
		}

		$this->loadDomainValues(SettingValue::DOMAIN_ID_COMMON);
		$this->loadDefaultValues();
	}

	/**
	 * @param int|null $domainId
	 */
	private function loadDomainValues($domainId) {
		if (!array_key_exists($domainId, $this->values)) {
			$this->values[$domainId] = [];
			foreach ($this->settingValueRepository->getAllByDomainId($domainId) as $settingValue) {
				/* @var $settingValue SettingValue */
				$this->values[$domainId][$settingValue->getName()] = $settingValue;
			}
		}
	}

	private function loadDefaultValues() {
		if ($this->valuesDefault === null) {
			$this->valuesDefault = [];
			foreach ($this->settingValueRepository->getAllDefault() as $settingValue) {
				/* @var $settingValue SettingValue */
				$this->valuesDefault[$settingValue->getName()] = $settingValue;
			}
		}
	}

	public function clearCache() {
		$this->values = [];
		$this->valuesDefault = null;
	}

}
