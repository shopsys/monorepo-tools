<?php

namespace Shopsys\ShopBundle\Model\Localization;

use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;

abstract class AbstractTranslatableEntity extends AbstractTranslatable
{
    /**
     * @Prezent\CurrentLocale
     */
    protected $currentLocale;

    /**
     * @var \Prezent\Doctrine\Translatable\TranslationInterface
     */
    protected $currentTranslation;

    /**
     * @return string|null
     */
    protected function getCurrentLocale()
    {
        return $this->currentLocale;
    }

    /**
     * @param string $locale
     * @return \Prezent\Doctrine\Translatable\TranslationInterface|null
     */
    protected function findTranslation($locale)
    {
        foreach ($this->getTranslations() as $translation) {
            if ($translation->getLocale() === $locale) {
                return $translation;
            }
        }

        return null;
    }

    /**
     * @param string|null $locale
     * @return \Prezent\Doctrine\Translatable\TranslationInterface
     */
    protected function translation($locale = null)
    {
        if ($locale === null) {
            $locale = $this->getCurrentLocale();
        }

        if (!$locale) {
            throw new \Shopsys\ShopBundle\Model\Localization\Exception\ImplicitLocaleNotSetException(
                $this,
                $this->id
            );
        }

        if ($this->currentTranslation && $this->currentTranslation->getLocale() === $locale) {
            return $this->currentTranslation;
        }

        $translation = $this->findTranslation($locale);
        if ($translation === null) {
            $translation = $this->createTranslation();
            $translation->setLocale($locale);
            $this->addTranslation($translation);
        }

        $this->currentTranslation = $translation;
        return $translation;
    }

    /**
     * return \Prezent\Doctrine\Translatable\TranslationInterface
     */
    abstract protected function createTranslation();
}
