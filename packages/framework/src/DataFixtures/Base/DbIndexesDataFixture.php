<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Base;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractNativeFixture;
use Shopsys\FrameworkBundle\Model\Localization\DbIndexesFacade;

class DbIndexesDataFixture extends AbstractNativeFixture implements DependentFixtureInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\DbIndexesFacade
     */
    private $dbIndexesFacade;

    public function __construct(DbIndexesFacade $dbIndexesFacade)
    {
        $this->dbIndexesFacade = $dbIndexesFacade;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->dbIndexesFacade->updateLocaleSpecificIndexes();

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

    public function getDependencies()
    {
        return [
            DbFunctionsDataFixture::class,
        ];
    }
}
