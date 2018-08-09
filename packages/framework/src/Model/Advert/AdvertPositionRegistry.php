<?php

namespace Shopsys\FrameworkBundle\Model\Advert;

class AdvertPositionRegistry
{
    /**
     * @return string[]
     */
    public function getAllLabelsIndexedByNames(): array
    {
        return [
            'header' => t('under heading'),
            'footer' => t('above footer'),
            'productList' => t('in category (above the category name)'),
            'leftSidebar' => t('in left panel (under category tree)'),
        ];
    }

    public function assertPositionNameIsKnown(string $positionName): void
    {
        $knownPositionsNames = array_keys($this->getAllLabelsIndexedByNames());
        if (!in_array($positionName, $knownPositionsNames, true)) {
            throw new \Shopsys\FrameworkBundle\Model\Advert\Exception\AdvertPositionNotKnownException(
                $positionName,
                $knownPositionsNames
            );
        }
    }
}
