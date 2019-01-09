<?php

namespace Shopsys\FrameworkBundle\Component\Domain\Multidomain;

/**
 * The class ensures proper functionality after the component is separated into standalone package independent of framework.
 * @see \Shopsys\FrameworkBundle\Component\Domain\Multidomain\MultidomainEntityClassFinderFacade.
 *
 * Now, in framework, yet another implementation is used. *
 * @see \Shopsys\FrameworkBundle\Model\MultidomainEntityClassProvider
 */
class DefaultMultidomainEntityClassProvider implements MultidomainEntityClassProviderInterface
{
    /**
     * @return string[]
     */
    public function getIgnoredMultidomainEntitiesNames(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function getManualMultidomainEntitiesNames(): array
    {
        return [];
    }
}
