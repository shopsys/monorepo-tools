<?php

namespace Shopsys\FrameworkBundle\Model\AdvancedSearch;

class AdvancedSearchConfig
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface[]
     */
    private $filters;

    public function __construct()
    {
        $this->filters = [];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface $filter
     */
    public function registerFilter(AdvancedSearchFilterInterface $filter)
    {
        if (array_key_exists($filter->getName(), $this->filters)) {
            $message = 'Filter "' . $filter->getName() . '" already exists.';
            throw new \Shopsys\FrameworkBundle\Model\AdvancedSearch\Exception\AdvancedSearchFilterAlreadyExistsException($message);
        }

        $this->filters[$filter->getName()] = $filter;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface[]
     */
    public function getAllFilters()
    {
        return $this->filters;
    }

    /**
     * @param string $filterName
     * @return \Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface
     */
    public function getFilter($filterName)
    {
        if (!array_key_exists($filterName, $this->filters)) {
            $message = 'Filter "' . $filterName . '" not found.';
            throw new \Shopsys\FrameworkBundle\Model\AdvancedSearch\Exception\AdvancedSearchFilterNotFoundException($message);
        }

        return $this->filters[$filterName];
    }
}
