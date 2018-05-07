<?php

namespace Shopsys\FrameworkBundle\Model\Localization;

class DbIndexesFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\Localization
     */
    private $localization;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\DbIndexesRepository
     */
    private $dbIndexesRepository;

    public function __construct(Localization $localization, DbIndexesRepository $dbIndexesRepository)
    {
        $this->localization = $localization;
        $this->dbIndexesRepository = $dbIndexesRepository;
    }

    public function updateLocaleSpecificIndexes(): void
    {
        foreach ($this->localization->getLocalesOfAllDomains() as $locale) {
            $domainCollation = $this->localization->getCollationByLocale($locale);
            $this->dbIndexesRepository->updateProductTranslationNameIndexForLocaleAndCollation($locale, $domainCollation);
        }
    }
}
