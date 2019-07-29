<?php

declare(strict_types=1);

namespace Shopsys\BackendApiBundle\Component\DataSetter;

use DateTime;
use Shopsys\FrameworkBundle\Component\Domain\Domain;

/**
 * @experimental
 */
class ApiDataSetter
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @param string $arrayKey
     * @param array $apiData
     * @param object $entityData
     * @param string|null $entityDataKey
     */
    public function setValueIfExists(string $arrayKey, array $apiData, object $entityData, ?string $entityDataKey = null): void
    {
        $entityDataKey = $entityDataKey ?: $arrayKey;

        if (array_key_exists($arrayKey, $apiData)) {
            $entityData->$entityDataKey = $apiData[$arrayKey];
        }
    }

    /**
     * @param string $arrayKey
     * @param array $apiData
     * @param object $entityData
     * @param string|null $entityDataKey
     */
    public function setDateTimeValueIfExists(string $arrayKey, array $apiData, object $entityData, ?string $entityDataKey = null): void
    {
        $entityDataKey = $entityDataKey ?: $arrayKey;

        if (array_key_exists($arrayKey, $apiData)) {
            $entityData->$entityDataKey = $apiData[$arrayKey] !== null ? new DateTime($apiData[$arrayKey]) : null;
        }
    }

    /**
     * @param string $arrayKey
     * @param array $apiData
     * @param object $entityData
     * @param string|null $entityDataKey
     */
    public function setMultidomainValueIfExists(string $arrayKey, array $apiData, object $entityData, ?string $entityDataKey = null): void
    {
        $entityDataKey = $entityDataKey ?: $arrayKey;

        if (array_key_exists($arrayKey, $apiData)) {
            foreach ($this->domain->getAllIds() as $domainId) {
                if (array_key_exists($domainId, $apiData[$arrayKey])) {
                    $entityData->$entityDataKey[$domainId] = $apiData[$arrayKey][$domainId];
                }
            }
        }
    }

    /**
     * @param string $arrayKey
     * @param array $apiData
     * @param object $entityData
     * @param string|null $entityDataKey
     */
    public function setMultilanguageValueIfExists(string $arrayKey, array $apiData, object $entityData, ?string $entityDataKey = null): void
    {
        $entityDataKey = $entityDataKey ?: $arrayKey;

        if (array_key_exists($arrayKey, $apiData)) {
            foreach ($this->domain->getAllLocales() as $locale) {
                if (array_key_exists($locale, $apiData[$arrayKey])) {
                    $entityData->$entityDataKey[$locale] = $apiData[$arrayKey][$locale];
                }
            }
        }
    }
}
