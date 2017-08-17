<?php

namespace Shopsys\ShopBundle\Model\PluginData;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="plugin_data_values")
 * @ORM\Entity
 */
class PluginDataValue
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $pluginName;

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $context;

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $jsonValue;

    /**
     * @param string $pluginName
     * @param string $context
     * @param string $key
     * @param mixed $value
     */
    public function __construct($pluginName, $context, $key, $value)
    {
        $this->pluginName = $pluginName;
        $this->context = $context;
        $this->key = $key;
        $this->jsonValue = json_encode($value);
    }

    /**
     * @param mixed $value
     */
    public function changeValue($value)
    {
        $this->jsonValue = json_encode($value);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return json_decode($this->jsonValue, true);
    }
}
