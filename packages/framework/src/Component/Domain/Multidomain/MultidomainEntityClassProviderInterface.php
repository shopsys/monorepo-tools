<?php

namespace Shopsys\FrameworkBundle\Component\Domain\Multidomain;

/**
 * MultidomainEntityClassFinder finds multidomain entities,
 * i.e. entities that have composite identifier including field called $domainId.
 *
 * This interface adds option to ignore some of these or manually add entities
 * that do not match the condition when multidomain data are created.
 * @see \Shopsys\FrameworkBundle\Command\CreateDomainsDataCommand
 */
interface MultidomainEntityClassProviderInterface
{
    /**
     * Return entities FQNs that have identifier called $domainId but is not multidomain entity.
     *
     * @return string[]
     */
    public function getIgnoredMultidomainEntitiesNames(): array;

    /**
     * Return entities FQNs that have $domainId property, but property itself is not identifier.
     *
     * @return string[]
     */
    public function getManualMultidomainEntitiesNames(): array;
}
