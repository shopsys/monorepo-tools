<?php

namespace Shopsys\ShopBundle\Component\Cron\Config;

use Shopsys\ShopBundle\Component\Cron\Config\CronConfig;
use Shopsys\ShopBundle\Component\Cron\Config\CronConfigLoader;
use Shopsys\ShopBundle\Component\Cron\CronTimeResolver;

class CronConfigFactory
{
    /**
     * @var \Shopsys\ShopBundle\Component\Cron\CronTimeResolver
     */
    private $cronTimeResolver;

    /**
     * @var \Shopsys\ShopBundle\Component\Cron\Config\CronConfigLoader
     */
    private $cronConfigLoader;

    public function __construct(CronTimeResolver $cronTimeResolver, CronConfigLoader $cronConfigLoader)
    {
        $this->cronConfigLoader = $cronConfigLoader;
        $this->cronTimeResolver = $cronTimeResolver;
    }

    /**
     * @param string $ymlFilepath
     * @return \Shopsys\ShopBundle\Component\Cron\Config\CronConfig
     */
    public function create($ymlFilepath)
    {
        $cronModuleConfigs = $this->cronConfigLoader->loadCronModuleConfigsFromYaml($ymlFilepath);

        return new CronConfig($this->cronTimeResolver, $cronModuleConfigs);
    }
}
