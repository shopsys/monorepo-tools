<?php

namespace Shopsys\FrameworkBundle\Model\Localization;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;

class DbIndexesRepository
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $locale
     * @param string $collation
     */
    public function updateProductTranslationNameIndexForLocaleAndCollation(string $locale, string $collation)
    {
        $this->em->createNativeQuery(
            'CREATE INDEX IF NOT EXISTS product_translations_name_' . $locale . '_idx
                ON product_translations (name COLLATE "' . $collation . '") WHERE locale = \':locale\'',
            new ResultSetMapping()
        )->execute(['locale' => $locale]);
    }
}
