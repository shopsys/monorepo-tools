<?php

namespace Shopsys\ShopBundle\Component\Setting;

use Doctrine\ORM\EntityManager;

class Setting
{
    const ORDER_SENT_PAGE_CONTENT = 'orderSubmittedText';
    const DEFAULT_PRICING_GROUP = 'defaultPricingGroupId';
    const DEFAULT_AVAILABILITY_IN_STOCK = 'defaultAvailabilityInStockId';
    const TERMS_AND_CONDITIONS_ARTICLE_ID = 'termsAndConditionsArticleId';
    const PRIVACY_POLICY_ARTICLE_ID = 'privacyPolicyArticleId';
    const COOKIES_ARTICLE_ID = 'cookiesArticleId';
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
     * @var \Shopsys\ShopBundle\Component\Setting\SettingValueRepository
     */
    private $settingValueRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Setting\SettingValue[][]
     */
    private $values;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Shopsys\ShopBundle\Component\Setting\SettingValueRepository $settingValueRepository
     */
    public function __construct(EntityManager $em, SettingValueRepository $settingValueRepository)
    {
        $this->em = $em;
        $this->settingValueRepository = $settingValueRepository;
        $this->clearCache();
    }

    /**
     * @param string $key
     * @return \DateTime|string|int|float|bool|null
     */
    public function get($key)
    {
        $this->loadDomainValues(SettingValue::DOMAIN_ID_COMMON);

        if (array_key_exists($key, $this->values[SettingValue::DOMAIN_ID_COMMON])) {
            $settingValue = $this->values[SettingValue::DOMAIN_ID_COMMON][$key];

            return $settingValue->getValue();
        }

        $message = 'Common setting value with name "' . $key . '" not found.';
        throw new \Shopsys\ShopBundle\Component\Setting\Exception\SettingValueNotFoundException($message);
    }

    /**
     * @param string $key
     * @param int $domainId
     * @return \DateTime|string|int|float|bool|null
     */
    public function getForDomain($key, $domainId)
    {
        $this->loadDomainValues($domainId);

        if (array_key_exists($key, $this->values[$domainId])) {
            $settingValue = $this->values[$domainId][$key];

            return $settingValue->getValue();
        }

        $message = 'Setting value with name "' . $key . '" for domain with ID "' . $domainId . '" not found.';
        throw new \Shopsys\ShopBundle\Component\Setting\Exception\SettingValueNotFoundException($message);
    }

    /**
     * @param string $key
     * @param \DateTime|string|int|float|bool|null $value
     */
    public function set($key, $value)
    {
        $this->loadDomainValues(SettingValue::DOMAIN_ID_COMMON);

        if (array_key_exists($key, $this->values[SettingValue::DOMAIN_ID_COMMON])) {
            $settingValue = $this->values[SettingValue::DOMAIN_ID_COMMON][$key];
            $settingValue->edit($value);

            $this->em->flush($settingValue);
        } else {
            $message = 'Common setting value with name "' . $key . '" not found.';
            throw new \Shopsys\ShopBundle\Component\Setting\Exception\SettingValueNotFoundException($message);
        }
    }

    /**
     * @param string $key
     * @param \DateTime|string|int|float|bool|null $value
     * @param int $domainId
     */
    public function setForDomain($key, $value, $domainId)
    {
        $this->loadDomainValues($domainId);

        if (array_key_exists($key, $this->values[$domainId])) {
            $settingValue = $this->values[$domainId][$key];
            $settingValue->edit($value);

            $this->em->flush($settingValue);
        } else {
            $message = 'Setting value with name "' . $key . '" for domain ID "' . $domainId . '" not found.';
            throw new \Shopsys\ShopBundle\Component\Setting\Exception\SettingValueNotFoundException($message);
        }
    }

    /**
     * @param int $domainId
     */
    private function loadDomainValues($domainId)
    {
        if ($domainId === null) {
            $message = 'Cannot load setting value for null domain ID';
            throw new \Shopsys\ShopBundle\Component\Setting\Exception\InvalidArgumentException($message);
        }

        if (!array_key_exists($domainId, $this->values)) {
            $this->values[$domainId] = [];
            foreach ($this->settingValueRepository->getAllByDomainId($domainId) as $settingValue) {
                $this->values[$domainId][$settingValue->getName()] = $settingValue;
            }
        }
    }

    public function clearCache()
    {
        $this->values = [];
    }
}
