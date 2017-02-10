<?php

namespace Shopsys\ShopBundle\Model\Heureka;

use Heureka\ShopCertification;

class HeurekaShopCertificationService {

    /**
     * @param string $locale
     * @return bool
     */
    public function isDomainLocaleSupported($locale) {
        try {
            $this->getLanguageIdByLocale($locale);
            return true;
        } catch (\Shopsys\ShopBundle\Model\Heureka\Exception\LocaleNotSupportedException $ex) {
            return false;
        }
    }

    /**
     * @param string $locale
     * @return int
     */
    public function getLanguageIdByLocale($locale) {
        $supportedLanguagesByLocale = [
            'cs' => ShopCertification::HEUREKA_CZ,
            'sk' => ShopCertification::HEUREKA_SK,
        ];

        if (array_key_exists($locale, $supportedLanguagesByLocale)) {
            return $supportedLanguagesByLocale[$locale];
        }

        $message = 'Locale "' . $locale . '" is not supported.';
        throw new \Shopsys\ShopBundle\Model\Heureka\Exception\LocaleNotSupportedException($message);
    }

    /**
     * @param string $locale
     * @return string|null
     */
    public function getServerNameByLocale($locale) {
        if ($locale === 'cs') {
            return 'Heureka.cz';
        } elseif ($locale === 'sk') {
            return 'Heureka.sk';
        }
    }

}
