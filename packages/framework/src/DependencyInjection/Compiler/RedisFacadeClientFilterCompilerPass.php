<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\DependencyInjection\Compiler;

use Shopsys\FrameworkBundle\Component\Redis\RedisFacade;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Filter clients wired into RedisFacade so only defined services are passed (because of BC)
 * @deprecated This class is deprecated since SSFW 7.3
 */
class RedisFacadeClientFilterCompilerPass implements CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        $redisFacadeDefinition = $container->getDefinition(RedisFacade::class);
        $filteredArguments = $this->getFilteredArguments($redisFacadeDefinition, $container);
        $redisFacadeDefinition->setArguments($filteredArguments);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\Definition $redisFacadeDefinition
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @return array
     */
    protected function getFilteredArguments(Definition $redisFacadeDefinition, ContainerBuilder $container): array
    {
        return array_map(
            function ($argument) use ($container) {
                return is_array($argument) ? $this->removeMissingReferences($argument, $container) : $argument;
            },
            $redisFacadeDefinition->getArguments()
        );
    }

    /**
     * @param array $array
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @return array
     */
    protected function removeMissingReferences(array $array, ContainerBuilder $container): array
    {
        return array_filter($array, function ($item) use ($container) {
            if ($item instanceof Reference && !$container->hasDefinition((string)$item)) {
                $message = sprintf('A missing service definition "%s" has been removed by "%s".', $item, __CLASS__);
                $message .= ' This compiler pass is deprecated since SSFW 7.3, you should define the missing service or remove the reference from the arguments.';
                @trigger_error($message, E_USER_DEPRECATED);

                return false;
            }

            return true;
        });
    }
}
