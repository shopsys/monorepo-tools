<?php

namespace Shopsys\IntegrationTestingBundle\ServiceLocator;

use ReflectionClass;
use ReflectionFunctionAbstract;
use Shopsys\IntegrationTestingBundle\ServiceLocator\Exception\AmbiguousTypeNameException;
use Shopsys\IntegrationTestingBundle\ServiceLocator\Exception\UnknownTypeNameException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Locates an instance of service from dependency injection container by its type name.
 */
class ServiceByTypeLocator
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var string[][]
     */
    private $serviceIdsByTypeName;

    /**
     * @param string $classNameByServiceIdMapFilename
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(
        $classNameByServiceIdMapFilename,
        ContainerInterface $container
    ) {
        $this->container = $container;

        $classNameByServiceId = json_decode(file_get_contents($classNameByServiceIdMapFilename), true);

        foreach ($classNameByServiceId as $serviceId => $className) {
            $this->serviceIdsByTypeName[$className][] = $serviceId;

            if (class_exists($className)) {
                $reflectionClass = new ReflectionClass($className);

                $implementedInterfaces = $reflectionClass->getInterfaceNames();
                foreach ($implementedInterfaces as $interfaceName) {
                    $this->serviceIdsByTypeName[$interfaceName][] = $serviceId;
                }

                while ($reflectionClass = $reflectionClass->getParentClass()) {
                    $this->serviceIdsByTypeName[$reflectionClass->getName()][] = $serviceId;
                }
            }
        }
    }

    /**
     * @param string $className
     * @return object
     */
    public function getByType($className)
    {
        if (array_key_exists($className, $this->serviceIdsByTypeName)) {
            $serviceIds = $this->serviceIdsByTypeName[$className];

            if (count($serviceIds) > 1) {
                throw new \Shopsys\IntegrationTestingBundle\ServiceLocator\Exception\AmbiguousTypeNameException(
                    $className,
                    $serviceIds
                );
            }

            $serviceId = reset($serviceIds);

            return $this->container->get($serviceId);
        }

        throw new \Shopsys\IntegrationTestingBundle\ServiceLocator\Exception\UnknownTypeNameException($className);
    }
}
