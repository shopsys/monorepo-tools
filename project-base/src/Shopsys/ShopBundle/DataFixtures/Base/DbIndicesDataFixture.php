<?php

namespace Shopsys\ShopBundle\DataFixtures\Base;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractNativeFixture;
use Shopsys\ShopBundle\Model\Localization\Localization;

class DbIndicesDataFixture extends AbstractNativeFixture implements DependentFixtureInterface {

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {
        $localization = $this->get(Localization::class);
        /* @var $localization \Shopsys\ShopBundle\Model\Localization\Localization */
        foreach ($localization->getAllLocales() as $locale) {
            $domainCollation = $localization->getCollationByLocale($locale);
            $this->executeNativeQuery('CREATE INDEX product_translations_name_' . $locale . '_idx
                ON product_translations (name COLLATE "' . $domainCollation . '") WHERE locale = \'' . $locale . '\'');
        }

        $this->executeNativeQuery('CREATE INDEX product_translations_name_normalize_idx
            ON product_translations (NORMALIZE(name))');
        $this->executeNativeQuery('CREATE INDEX product_catnum_normalize_idx
            ON products (NORMALIZE(catnum))');
        $this->executeNativeQuery('CREATE INDEX product_partno_normalize_idx
            ON products (NORMALIZE(partno))');
        $this->executeNativeQuery('CREATE INDEX order_email_normalize_idx
            ON orders (NORMALIZE(email))');
        $this->executeNativeQuery('CREATE INDEX order_last_name_normalize_idx
            ON orders (NORMALIZE(last_name))');
        $this->executeNativeQuery('CREATE INDEX order_company_name_normalize_idx
            ON orders (NORMALIZE(company_name))');
        $this->executeNativeQuery('CREATE INDEX user_email_normalize_idx
            ON users (NORMALIZE(email))');
    }

    public function getDependencies() {
        return [
            DbFunctionsDataFixture::class,
        ];
    }

}
