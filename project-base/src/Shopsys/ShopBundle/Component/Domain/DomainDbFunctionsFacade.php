<?php

namespace Shopsys\ShopBundle\Component\Domain;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Shopsys\ShopBundle\Component\Domain\Domain;

class DomainDbFunctionsFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(EntityManager $em, Domain $domain)
    {
        $this->em = $em;
        $this->domain = $domain;
    }

    public function createDomainDbFunctions()
    {
        $this->createDomainIdsByLocaleFunction();
        $this->createLocaleByDomainIdFunction();
    }

    private function createDomainIdsByLocaleFunction()
    {
        $domainsIdsByLocale = [];
        foreach ($this->domain->getAllIncludingDomainConfigsWithoutDataCreated() as $domainConfig) {
            $domainsIdsByLocale[$domainConfig->getLocale()][] = $domainConfig->getId();
        }

        $domainIdsByLocaleSqlClauses = [];
        foreach ($domainsIdsByLocale as $locale => $domainIds) {
            $sql = 'WHEN locale = \'' . $locale . '\' THEN ';
            foreach ($domainIds as $domainId) {
                $sql .= ' RETURN NEXT ' . $domainId . ';';
            }
            $domainIdsByLocaleSqlClauses[] = $sql;
        }

        $query = $this->em->createNativeQuery('
            CREATE OR REPLACE FUNCTION get_domain_ids_by_locale(locale text) RETURNS SETOF integer AS $$
            BEGIN
                CASE
                    ' . implode("\n", $domainIdsByLocaleSqlClauses) . '
                    ELSE RAISE EXCEPTION \'Locale % does not exists\', locale;
                END CASE;
            END
            $$ LANGUAGE plpgsql IMMUTABLE;
            ',
            new ResultSetMapping()
        );

        return $query->execute();
    }

    private function createLocaleByDomainIdFunction()
    {
        $localeByDomainIdSqlClauses = [];
        foreach ($this->domain->getAllIncludingDomainConfigsWithoutDataCreated() as $domainConfig) {
            $localeByDomainIdSqlClauses[] =
                'WHEN domain_id = ' . $domainConfig->getId()
                . ' THEN RETURN \'' . $domainConfig->getLocale() . '\';';
        }

        $query = $this->em->createNativeQuery('
            CREATE OR REPLACE FUNCTION get_domain_locale(domain_id integer) RETURNS text AS $$
            BEGIN
                CASE
                    ' . implode("\n", $localeByDomainIdSqlClauses) . '
                    ELSE RAISE EXCEPTION \'Domain with ID % does not exists\', domain_id;
                END CASE;
            END
            $$ LANGUAGE plpgsql IMMUTABLE;
            ',
            new ResultSetMapping()
        );

        return $query->execute();
    }
}
