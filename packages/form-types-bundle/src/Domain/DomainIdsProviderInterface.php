<?php

namespace Shopsys\FormTypesBundle\Domain;

interface DomainIdsProviderInterface
{
    /**
     * @return int[]
     */
    public function getAllIds();
}
