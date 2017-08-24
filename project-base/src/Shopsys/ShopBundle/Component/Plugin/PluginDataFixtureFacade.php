<?php

namespace Shopsys\ShopBundle\Component\Plugin;

class PluginDataFixtureFacade
{
    /**
     * @var \Shopsys\ShopBundle\Component\Plugin\PluginDataFixtureRegistry
     */
    private $pluginDataFixtureRegistry;

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
