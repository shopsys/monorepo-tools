<?php

namespace Shopsys\ShopBundle\Component\Translation;

use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

class Translator implements TranslatorInterface, TranslatorBagInterface {

    const DEFAULT_DOMAIN = 'messages';
    const NOT_TRANSLATED_PREFIX = '##';
    const SOURCE_LOCALE = 'en';

    /**
     * @var \Shopsys\ShopBundle\Component\Translation\Translator|null
     */
    private static $self;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $originalTranslator;

    /**
     * @var \Symfony\Component\Translation\TranslatorBagInterface
     */
    private $originalTranslatorBag;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $identityTranslator;

    /**
     * @var \Shopsys\ShopBundle\Component\Translation\MessageIdNormalizer
     */
    private $messageIdNormalizer;

    public function __construct(
        TranslatorInterface $originalTranslator,
        TranslatorBagInterface $originalTranslatorBag,
        TranslatorInterface $identityTranslator,
        MessageIdNormalizer $messageIdNormalizer
    ) {
        $this->originalTranslator = $originalTranslator;
        $this->originalTranslatorBag = $originalTranslatorBag;
        $this->identityTranslator = $identityTranslator;
        $this->messageIdNormalizer = $messageIdNormalizer;
    }

    /**
     * Adds self::NOT_TRANSLATED_PREFIX to messages that are not translated and $locale
     * is not self::SOURCE_LOCALE. Passes trans() call to original translator
     * for logging purposes.
     * {@inheritdoc}
     */
    public function trans($id, array $parameters = [], $domain = null, $locale = null) {
        $normalizedId = $this->messageIdNormalizer->normalizeMessageId($id);
        $resolvedLocale = $this->resolveLocale($locale);
        $resolvedDomain = $this->resolveDomain($domain);

        $catalogue = $this->originalTranslatorBag->getCatalogue($resolvedLocale);

        if ($resolvedLocale === self::SOURCE_LOCALE) {
            if ($catalogue->defines($normalizedId, $resolvedDomain)) {
                $message = $this->originalTranslator->trans($normalizedId, $parameters, $resolvedDomain, $resolvedLocale);
            } else {
                $message = $this->identityTranslator->trans($normalizedId, $parameters, $resolvedDomain, $resolvedLocale);
            }
        } else {
            $message = $this->originalTranslator->trans($normalizedId, $parameters, $resolvedDomain, $resolvedLocale);

            if (!$catalogue->has($normalizedId, $resolvedDomain)) {
                $message = self::NOT_TRANSLATED_PREFIX . $message;
            }
        }

        return $message;
    }

    /**
     * Adds self::NOT_TRANSLATED_PREFIX to messages that are not translated and $locale
     * is not self::SOURCE_LOCALE. Passes transChoice() call to original translator
     * for logging purposes.
     * {@inheritdoc}
     */
    public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null) {
        $normalizedId = $this->messageIdNormalizer->normalizeMessageId($id);
        $resolvedLocale = $this->resolveLocale($locale);
        $resolvedDomain = $this->resolveDomain($domain);

        $catalogue = $this->originalTranslatorBag->getCatalogue($resolvedLocale);

        if ($resolvedLocale === self::SOURCE_LOCALE) {
            if ($catalogue->defines($normalizedId, $resolvedDomain)) {
                $message = $this->originalTranslator->transChoice($normalizedId, $number, $parameters, $resolvedDomain, $resolvedLocale);
            } else {
                $message = $this->identityTranslator->transChoice($normalizedId, $number, $parameters, $resolvedDomain, $resolvedLocale);
            }
        } else {
            $message = $this->originalTranslator->transChoice($normalizedId, $number, $parameters, $resolvedDomain, $resolvedLocale);

            if (!$catalogue->has($normalizedId, $resolvedDomain)) {
                $message = self::NOT_TRANSLATED_PREFIX . $message;
            }
        }

        return $message;
    }

    /**
     * @param string|null $locale
     * @return string|null
     */
    private function resolveLocale($locale) {
        if ($locale === null) {
            return $this->getLocale();
        }

        return $locale;
    }

    /**
     * @param string|null $domain
     * @return string
     */
    private function resolveDomain($domain) {
        if ($domain === null) {
            return self::DEFAULT_DOMAIN;
        }

        return $domain;
    }

    /**
     * {@inheritDoc}
     */
    public function getLocale() {
        return $this->originalTranslator->getLocale();
    }

    /**
     * {@inheritDoc}
     */
    public function setLocale($locale) {
        $this->originalTranslator->setLocale($locale);
        $this->identityTranslator->setLocale($locale);
    }

    /**
     * {@inheritDoc}
     */
    public function getCatalogue($locale = null) {
        return $this->originalTranslatorBag->getCatalogue($locale);
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Translation\Translator $translator
     */
    public static function injectSelf(Translator $translator) {
        self::$self = $translator;
    }

    /**
     * @param string $id
     * @param array $parameters
     * @param string|null $domain
     * @param string|null $locale
     * @return string
     */
    public static function staticTrans($id, array $parameters = [], $domain = null, $locale = null) {
        if (self::$self === null) {
            throw new \Shopsys\ShopBundle\Component\Translation\Exception\InstanceNotInjectedException();
        }

        return self::$self->trans($id, $parameters, $domain, $locale);
    }

    /**
     * @param string $id
     * @param int $number
     * @param array $parameters
     * @param string|null $domain
     * @param string|null $locale
     * @return string
     */
    public static function staticTransChoice($id, $number, array $parameters = [], $domain = null, $locale = null) {
        if (self::$self === null) {
            throw new \Shopsys\ShopBundle\Component\Translation\Exception\InstanceNotInjectedException();
        }

        return self::$self->transChoice($id, $number, $parameters, $domain, $locale);
    }

}
