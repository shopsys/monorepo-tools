<?php

namespace Shopsys\FrameworkBundle\Component\Plugin;

use Shopsys\Plugin\PluginDataFixtureInterface;

class PluginDataFixtureRegistry
{
    /**
     * @var \Shopsys\Plugin\PluginDataFixtureInterface[]
     */
    private $pluginDataFixtures = [];

    /**
     * @param \Shopsys\Plugin\PluginDataFixtureInterface $pluginDataFixture
     */
    public function registerDataFixture(PluginDataFixtureInterface $pluginDataFixture)
    {
        $this->pluginDataFixtures[] = $pluginDataFixture;
    }

    /**
     * @return \Shopsys\Plugin\PluginDataFixtureInterface[]
     */
    public function getDataFixtures()
    {
        return $this->pluginDataFixtures;
    }
}
