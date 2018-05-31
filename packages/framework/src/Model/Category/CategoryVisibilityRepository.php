<?php

namespace Shopsys\FrameworkBundle\Model\Category;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class CategoryVisibilityRepository
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryVisibilityRecalculationScheduler
     */
    private $categoryVisibilityRecalculationScheduler;

    public function __construct(
        EntityManagerInterface $em,
        Domain $domain,
        CategoryVisibilityRecalculationScheduler $categoryVisibilityRecalculationScheduler
    ) {
        $this->em = $em;
        $this->domain = $domain;
        $this->categoryVisibilityRecalculationScheduler = $categoryVisibilityRecalculationScheduler;
    }

    public function refreshCategoriesVisibility()
    {
        $domains = $this->domain->getAll();
        foreach ($domains as $domainConfig) {
            $this->refreshCategoriesVisibilityOnDomain($domainConfig);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     */
    private function refreshCategoriesVisibilityOnDomain(DomainConfig $domainConfig)
    {
        $this->setRootCategoryVisibleOnDomain($domainConfig);

        $maxLevel = $this->getMaxLevelOnDomain($domainConfig);

        for ($level = 1; $level <= $maxLevel; $level++) {
            $this->refreshCategoriesVisibilityOnDomainAndLevel($domainConfig, $level);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     */
    private function setRootCategoryVisibleOnDomain(DomainConfig $domainConfig)
    {
        $this->em->getConnection()->executeUpdate(
            'UPDATE category_domains AS cd
                SET visible = TRUE

            FROM categories AS c
            WHERE c.id = cd.category_id
                AND cd.domain_id = :domainId
                AND c.parent_id IS NULL
            ',
            [
                'domainId' => $domainConfig->getId(),
            ]
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return int
     */
    private function getMaxLevelOnDomain(DomainConfig $domainConfig)
    {
        return $this->em->getConnection()->fetchColumn(
            'SELECT MAX(c.level)
            FROM categories c
            JOIN category_domains cd ON cd.category_id = c.id AND cd.domain_id = :domainId
            ',
            [
                'domainId' => $domainConfig->getId(),
            ]
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param int $level
     */
    private function refreshCategoriesVisibilityOnDomainAndLevel(DomainConfig $domainConfig, $level)
    {
        $this->em->getConnection()->executeUpdate(
            'UPDATE category_domains AS cd
                SET visible = (
                    cd.enabled = TRUE
                    AND
                    ct.name IS NOT NULL
                    AND
                    parent_cd.visible = TRUE
                )

            FROM categories AS c
            LEFT JOIN category_translations ct ON ct.translatable_id = c.id AND ct.locale = :locale
            JOIN category_domains AS parent_cd ON parent_cd.category_id = c.parent_id AND parent_cd.domain_id = :domainId
            WHERE c.id = cd.category_id
                AND cd.domain_id = :domainId
                AND c.level = :level
            ',
            [
                'domainId' => $domainConfig->getId(),
                'locale' => $domainConfig->getLocale(),
                'level' => $level,
            ]
        );
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if ($this->categoryVisibilityRecalculationScheduler->isRecalculationScheduled()) {
            try {
                $this->em->beginTransaction();
                $this->refreshCategoriesVisibility();
                $this->em->commit();
            } catch (\Exception $ex) {
                $this->em->rollback();
                throw $ex;
            }
        }
    }
}
