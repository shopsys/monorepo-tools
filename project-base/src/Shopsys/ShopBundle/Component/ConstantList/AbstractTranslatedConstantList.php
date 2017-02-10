<?php

namespace Shopsys\ShopBundle\Component\ConstantList;

use Shopsys\ShopBundle\Component\ConstantList\TranslatedConstantListInterface;

abstract class AbstractTranslatedConstantList implements TranslatedConstantListInterface
{

    /**
     * @return string
     */
    public function getValues() {
        return array_keys($this->getTranslationsIndexedByValue());
    }

    /**
     * @param string $constant
     */
    public function getTranslation($constant) {
        $translations = $this->getTranslationsIndexedByValue();
        if (!array_key_exists($constant, $translations)) {
            throw new \Shopsys\ShopBundle\Component\ConstantList\Exception\UndefinedTranslationException($constant);
        }

        return $translations[$constant];
    }

    /**
     * @return string[string]
     */
    abstract public function getTranslationsIndexedByValue();

}
