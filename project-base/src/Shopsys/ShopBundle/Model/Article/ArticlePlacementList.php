<?php

namespace Shopsys\ShopBundle\Model\Article;

use Shopsys\ShopBundle\Component\ConstantList\AbstractTranslatedConstantList;

class ArticlePlacementList extends AbstractTranslatedConstantList {

    const PLACEMENT_TOP_MENU = 'topMenu';
    const PLACEMENT_FOOTER = 'footer';
    const PLACEMENT_NONE = 'none';

    /**
     * @inheritdoc
     */
    public function getTranslationsIndexedByValue() {
        return [
            self::PLACEMENT_TOP_MENU => t('in upper menu'),
            self::PLACEMENT_FOOTER => t('in footer'),
            self::PLACEMENT_NONE => t('without positoning'),
        ];
    }

}
