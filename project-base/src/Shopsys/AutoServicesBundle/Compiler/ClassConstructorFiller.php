<?php

namespace Shopsys\AutoServicesBundle\Compiler;

use ReflectionMethod;
use Shopsys\AutoServicesBundle\Compiler\ContainerClassList;
use Shopsys\AutoServicesBundle\Compiler\ParameterProcessor;
use Symfony\Component\DependencyInjection\Definition;

class ClassConstructorFiller
{
    /**
     * @var ParameterProcessor
     */
    private $parameterProcessor;

    public function __construct(ParameterProcessor $parameterProcessor) {
        $this->parameterProcessor = $parameterProcessor;
        $this->parameterProcessor->injectClassConstructorFilter($this);
    }

    /**
     * @param \ReflectionMethod|null $constructor
     * @param string $serviceId
     * @param \Symfony\Component\DependencyInjection\Definition $definition
     * @param \Shopsys\AutoServicesBundle\Compiler\ContainerClassList $containerClassList
     */
    public function autowireParams($constructor, $serviceId, Definition $definition, ContainerClassList $containerClassList) {
        $explicitlyDefinedArguments = $definition->getArguments();
        $allArguments = [];

        if ($constructor instanceof ReflectionMethod) {
            foreach ($constructor->getParameters() as $index => $parameter) {
                if (array_key_exists($index, $explicitlyDefinedArguments)) {
                    $allArguments[] = $explicitlyDefinedArguments[$index];
                } elseif ($parameter->isDefaultValueAvailable()) {
                    $allArguments[] = $parameter->getDefaultValue();
                } else {
                    $allArguments[] = $this->parameterProcessor->getParameterValue($parameter, $serviceId, $containerClassList);
                }
            }
        }

        $definition->setArguments($allArguments);
    }
}
