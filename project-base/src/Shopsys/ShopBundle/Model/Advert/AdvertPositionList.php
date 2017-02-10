<?php

namespace Shopsys\ShopBundle\Model\Advert;

use Shopsys\ShopBundle\Component\ConstantList\AbstractTranslatedConstantList;

class AdvertPositionList extends AbstractTranslatedConstantList
{
    const POSITION_HEADER = 'header';
    const POSITION_FOOTER = 'footer';
    const POSITION_PRODUCT_LIST = 'productList';
    const POSITION_LEFT_SIDEBAR = 'leftSidebar';

    /**
     * @inheritdoc
     */
    public function getTranslationsIndexedByValue() {
        return [
            self::POSITION_HEADER => t('under heading'),
            self::POSITION_FOOTER => t('above footer'),
            self::POSITION_PRODUCT_LIST => t('in category (above the category name)'),
            self::POSITION_LEFT_SIDEBAR => t('in left panel (under category tree)'),
        ];
    }
}
