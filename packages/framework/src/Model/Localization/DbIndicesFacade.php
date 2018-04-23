<?php

namespace Shopsys\FrameworkBundle\Model\Localization;

class DbIndicesFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\Localization
     */
    private $localization;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\DbIndicesRepository
     */
    private $dbIndicesRepository;

    public function __construct(Localization $localization, DbIndicesRepository $dbIndicesRepository)
    {
        $this->localization = $localization;
        $this->dbIndicesRepository = $dbIndicesRepository;
    }

    public function updateLocaleSpecificIndices(): void
    {
        foreach ($this->localization->getLocalesOfAllDomains() as $locale) {
            $domainCollation = $this->localization->getCollationByLocale($locale);
            $this->dbIndicesRepository->updateProductTranslationNameIndexForLocaleAndCollation($locale, $domainCollation);
        }
    }
}
