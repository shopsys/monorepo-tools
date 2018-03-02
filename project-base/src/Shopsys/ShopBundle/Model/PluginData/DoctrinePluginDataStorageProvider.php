<?php

namespace Shopsys\FrameworkBundle\Model\PluginData;

use Doctrine\ORM\EntityManager;
use Shopsys\Plugin\PluginDataStorageProviderInterface;

class DoctrinePluginDataStorageProvider implements PluginDataStorageProviderInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\Plugin\DataStorageInterface[][]
     */
    private $dataStorages;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->dataStorages = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataStorage($pluginName, $context = self::CONTEXT_DEFAULT)
    {
        if (!isset($this->dataStorages[$pluginName][$context])) {
            $dataStorage = new DoctrinePluginDataStorage($this->em, $pluginName, $context);

            $this->dataStorages[$pluginName][$context] = $dataStorage;
        }

        return $this->dataStorages[$pluginName][$context];
    }
}
