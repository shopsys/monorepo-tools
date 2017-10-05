<?php

namespace Shopsys\ShopBundle\Model\PluginData;

use Doctrine\ORM\EntityManager;
use Shopsys\Plugin\DataStorageInterface;

class DoctrinePluginDataStorage implements DataStorageInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var string
     */
    private $pluginName;

    /**
     * @var string
     */
    private $context;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param string $pluginName
     * @param string $context
     */
    public function __construct(EntityManager $em, $pluginName, $context)
    {
        $this->em = $em;
        $this->pluginName = $pluginName;
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        $pluginDataValue = $this->findPluginDataValueByKey($key);

        return $pluginDataValue !== null ? $pluginDataValue->getValue() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple(array $keys)
    {
        $valuesByKey = [];
        foreach ($this->getPluginDataValuesByKeys($keys) as $pluginDataValue) {
            if ($pluginDataValue->getValue() !== null) {
                $valuesByKey[$pluginDataValue->getKey()] = $pluginDataValue->getValue();
            }
        }

        return $valuesByKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        $valuesByKey = [];
        foreach ($this->getAllPluginDataValues() as $pluginDataValue) {
            if ($pluginDataValue->getValue() !== null) {
                $valuesByKey[$pluginDataValue->getKey()] = $pluginDataValue->getValue();
            }
        }

        return $valuesByKey;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        if ($value === null) {
            $this->remove($key);
            return;
        }

        $pluginDataValue = $this->findPluginDataValueByKey($key);

        if ($pluginDataValue !== null) {
            $pluginDataValue->changeValue($value);
        } else {
            $pluginDataValue = new PluginDataValue($this->pluginName, $this->context, $key, $value);
            $this->em->persist($pluginDataValue);
        }

        $this->em->flush($pluginDataValue);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        $pluginDataValue = $this->findPluginDataValueByKey($key);

        if ($pluginDataValue !== null) {
            $this->em->remove($pluginDataValue);
            $this->em->flush($pluginDataValue);
        }
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getPluginDataRepository()
    {
        return $this->em->getRepository(PluginDataValue::class);
    }

    /**
     * @param string $key
     * @return \Shopsys\ShopBundle\Model\PluginData\PluginDataValue|null
     */
    private function findPluginDataValueByKey($key)
    {
        return $this->getPluginDataRepository()->findOneBy([
            'pluginName' => $this->pluginName,
            'context' => $this->context,
            'key' => $key,
        ]);
    }

    /**
     * @param string[] $keys
     * @return \Shopsys\ShopBundle\Model\PluginData\PluginDataValue[]
     */
    private function getPluginDataValuesByKeys(array $keys)
    {
        return $this->getPluginDataRepository()->findBy([
            'pluginName' => $this->pluginName,
            'context' => $this->context,
            'key' => $keys,
        ]);
    }

    /**
     * @return \Shopsys\ShopBundle\Model\PluginData\PluginDataValue[]
     */
    private function getAllPluginDataValues()
    {
        return $this->getPluginDataRepository()->findBy([
            'pluginName' => $this->pluginName,
            'context' => $this->context,
        ]);
    }
}
