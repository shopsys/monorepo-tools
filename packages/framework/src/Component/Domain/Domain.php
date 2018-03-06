<?php

namespace Shopsys\FrameworkBundle\Component\Domain;

use Shopsys\FormTypesBundle\Domain\DomainIdsProviderInterface;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Symfony\Component\HttpFoundation\Request;

class Domain implements DomainIdsProviderInterface
{
    const FIRST_DOMAIN_ID = 1;
    const MAIN_ADMIN_DOMAIN_ID = 1;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig|null
     */
    private $currentDomainConfig;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig[]
     */
    private $domainConfigs;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig[] $domainConfigs
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     */
    public function __construct(array $domainConfigs, Setting $setting)
    {
        $this->domainConfigs = $domainConfigs;
        $this->setting = $setting;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getCurrentDomainConfig()->getId();
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->getCurrentDomainConfig()->getLocale();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getCurrentDomainConfig()->getName();
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->getCurrentDomainConfig()->getUrl();
    }

    /**
     * @return bool
     */
    public function isHttps()
    {
        return $this->getCurrentDomainConfig()->isHttps();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig[]
     */
    public function getAll()
    {
        $domainConfigsWithDataCreated = [];
        foreach ($this->domainConfigs as $domainConfig) {
            $domainId = $domainConfig->getId();
            try {
                $this->setting->getForDomain(Setting::DOMAIN_DATA_CREATED, $domainId);
                $domainConfigsWithDataCreated[] = $domainConfig;
            } catch (\Shopsys\FrameworkBundle\Component\Setting\Exception\SettingValueNotFoundException $ex) {
            }
        }

        return $domainConfigsWithDataCreated;
    }

    /**
     * @return int[]
     */
    public function getAllIds()
    {
        $ids = [];
        foreach ($this->getAll() as $domainConfig) {
            $ids[] = $domainConfig->getId();
        }

        return $ids;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig[]
     */
    public function getAllIncludingDomainConfigsWithoutDataCreated()
    {
        return $this->domainConfigs;
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig
     */
    public function getDomainConfigById($domainId)
    {
        foreach ($this->domainConfigs as $domainConfig) {
            if ($domainId === $domainConfig->getId()) {
                return $domainConfig;
            }
        }

        throw new \Shopsys\FrameworkBundle\Component\Domain\Exception\InvalidDomainIdException();
    }

    /**
     * @param int $domainId
     */
    public function switchDomainById($domainId)
    {
        $this->currentDomainConfig = $this->getDomainConfigById($domainId);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function switchDomainByRequest(Request $request)
    {
        // Request::getBasePath() never contains script file name (/index.php)
        $url = $request->getSchemeAndHttpHost() . $request->getBasePath();

        foreach ($this->domainConfigs as $domainConfig) {
            if ($domainConfig->getUrl() === $url) {
                $this->currentDomainConfig = $domainConfig;
                return;
            }
        }

        throw new \Shopsys\FrameworkBundle\Component\Domain\Exception\UnableToResolveDomainException($url);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig
     */
    public function getCurrentDomainConfig()
    {
        if ($this->currentDomainConfig === null) {
            throw new \Shopsys\FrameworkBundle\Component\Domain\Exception\NoDomainSelectedException();
        }

        return $this->currentDomainConfig;
    }

    /**
     * @return bool
     */
    public function isMultidomain()
    {
        return count($this->getAll()) > 1;
    }
}
