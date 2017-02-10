<?php

namespace Shopsys\AutoServicesBundle\Compiler;

use ReflectionClass;
use ReflectionFunctionAbstract;
use Shopsys\AutoServicesBundle\Compiler\AutoServicesCollector;
use Shopsys\AutoServicesBundle\Compiler\ContainerClassList;
use Shopsys\AutoServicesBundle\Compiler\ServiceHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ScopeInterface;

class AutoContainer implements ContainerInterface
{

    /**
     * @var \Shopsys\AutoServicesBundle\Compiler\ServiceHelper
     */
    private $serviceHelper;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \Shopsys\AutoServicesBundle\Compiler\ContainerClassList
     */
    private $containerClassList;

    /**
     * @var \Shopsys\AutoServicesBundle\Compiler\AutoServicesCollector
     */
    private $autoServiceCollector;

    public function __construct(
        ContainerInterface $container,
        ServiceHelper $serviceHelper,
        ContainerClassList $containerClassList,
        AutoServicesCollector $autoServiceCollector
    ) {
        $this->serviceHelper = $serviceHelper;
        $this->container = $container;
        $this->containerClassList = $containerClassList;
        $this->autoServiceCollector = $autoServiceCollector;
    }

    public function get($serviceId, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE) {
        if ($this->serviceHelper->isServiceId($serviceId) && $this->container->has($serviceId)) {
            return $this->container->get($serviceId, $invalidBehavior);
        }

        try {
            return $this->getServiceByClassName($serviceId);
        } catch (\Exception $e) {
            if (self::EXCEPTION_ON_INVALID_REFERENCE === $invalidBehavior) {
                throw new \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException($serviceId, null, $e);
            }
            return null;
        }
    }

    /**
     * @param string $className
     * @return object
     */
    private function getServiceByClassName($className) {
        try {
            $classServiceId = $this->containerClassList->getServiceIdByClass($className);
            return $this->container->get($classServiceId);
        } catch (\Shopsys\AutoServicesBundle\Compiler\Exception\ServiceClassNotFoundException $e) {
            return $this->createServiceByClassName($className);
        } catch (\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException $e) {
            // In crawler test kernel boots more times (each call the method getClient()).
            // Container can be invalidated, but file with container class is already included
            // from first boot (require_once cannot redeclare the class).
            // Therefore $this->container does not contain the service, but $this->containerClassList does.
            return $this->createServiceByClassName($className);
        }
    }

    /**
     * @param string $serviceId
     * @param object $service
     * @param string $className
     */
    private function registerServiceToContainer($serviceId, $service, $className) {
        $this->container->set($serviceId, $service);
        $this->autoServiceCollector->addService($serviceId, $className);
        $this->containerClassList->addClass($serviceId, $className);
    }

    /**
     * @param string $className
     * @return object
     */
    private function createServiceByClassName($className) {
        if (!$this->serviceHelper->canBeService($className)) {
            throw new \Shopsys\AutoServicesBundle\Compiler\Exception\ServiceClassNotFoundException($className);
        }

        $classServiceId = $this->serviceHelper->convertClassNameToServiceId($className);
        $reflectionClass = new ReflectionClass($className);
        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            $service = new $className();
        } else {
            $service = $reflectionClass->newInstanceArgs($this->getConstructorArguments($constructor));
        }

        $this->registerServiceToContainer($classServiceId, $service, $className);

        return $service;
    }

    /**
     * @param \ReflectionFunctionAbstract $constructor
     * @return array
     */
    private function getConstructorArguments(ReflectionFunctionAbstract $constructor) {
        $arguments = [];
        foreach ($constructor->getParameters() as $parameter) {
            /* @var $parameter \ReflectionParameter */
            if ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
            } else {
                $argumentClassName = $parameter->getClass()->name;
                $arguments[] = $this->getServiceByClassName($argumentClassName);
            }
        }
        return $arguments;
    }

    public function addScope(ScopeInterface $scope) {
        $this->container->addScope($scope);
    }

    public function enterScope($name) {
        $this->container->enterScope($name);
    }

    public function getParameter($name) {
        return $this->container->getParameter($name);
    }

    public function has($id) {
        try {
            $this->get($id, self::EXCEPTION_ON_INVALID_REFERENCE);
            return true;
        } catch (\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException $e) {
            return false;
        }
    }

    public function hasParameter($name) {
        return $this->container->hasParameter($name);
    }

    public function hasScope($name) {
        return $this->container->hasScope($name);
    }

    public function isScopeActive($name) {
        return $this->container->isScopeActive($name);
    }

    public function leaveScope($name) {
        $this->container->leaveScope($name);
    }

    public function set($id, $service, $scope = self::SCOPE_CONTAINER) {
        $this->container->set($id, $service, $scope);
    }

    public function setParameter($name, $value) {
        $this->container->setParameter($name, $value);
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface
     */
    public function getParameterBag() {
        return $this->container->getParameterBag();
    }
}
