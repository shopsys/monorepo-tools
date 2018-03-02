<?php

namespace Shopsys\FrameworkBundle\Component\Plugin;

use Shopsys\FrameworkBundle\Component\Plugin\Exception\PluginCrudExtensionAlreadyRegisteredException;
use Shopsys\FrameworkBundle\Component\Plugin\Exception\UnknownPluginCrudExtensionTypeException;
use Shopsys\FrameworkBundle\Component\String\TransformString;
use Shopsys\Plugin\PluginCrudExtensionInterface;

class PluginCrudExtensionRegistry
{
    const KNOWN_TYPES = [
        'product',
        'category',
    ];

    /**
     * @var \Shopsys\Plugin\PluginCrudExtensionInterface[][]
     */
    private $crudExtensionsByTypeAndServiceId = [];

    /**
     * @param \Shopsys\Plugin\PluginCrudExtensionInterface $crudExtension
     * @param string $type
     * @param string $serviceId
     */
    public function registerCrudExtension(PluginCrudExtensionInterface $crudExtension, $type, $serviceId)
    {
        self::assertTypeIsKnown($type);
        $key = TransformString::stringToCamelCase($serviceId);

        if (isset($this->crudExtensionsByTypeAndServiceId[$type][$key])) {
            throw new PluginCrudExtensionAlreadyRegisteredException($type, $key);
        }

        $this->crudExtensionsByTypeAndServiceId[$type][$key] = $crudExtension;
    }

    /**
     * @param string $type
     * @return \Shopsys\Plugin\PluginCrudExtensionInterface[]
     */
    public function getCrudExtensions($type)
    {
        return $this->crudExtensionsByTypeAndServiceId[$type] ?? [];
    }

    /**
     * @param string $type
     */
    public static function assertTypeIsKnown($type)
    {
        if (!in_array($type, self::KNOWN_TYPES, true)) {
            throw new UnknownPluginCrudExtensionTypeException($type, self::KNOWN_TYPES);
        }
    }
}
