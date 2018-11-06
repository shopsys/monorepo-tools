<?php

namespace Shopsys\FrameworkBundle\Component\Plugin;

class PluginDataFixtureFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Plugin\PluginDataFixtureRegistry
     */
    protected $pluginDataFixtureRegistry;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Plugin\PluginDataFixtureRegistry $pluginDataFixtureRegistry
     */
    public function __construct(PluginDataFixtureRegistry $pluginDataFixtureRegistry)
    {
        $this->pluginDataFixtureRegistry = $pluginDataFixtureRegistry;
    }

    public function loadAll()
    {
        $pluginDataFixtures = $this->pluginDataFixtureRegistry->getDataFixtures();
        foreach ($pluginDataFixtures as $pluginDataFixture) {
            $pluginDataFixture->load();
        }
    }
}
