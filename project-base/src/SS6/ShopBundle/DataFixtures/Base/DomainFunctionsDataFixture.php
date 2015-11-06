<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractNativeFixture;
use SS6\ShopBundle\Component\Domain\Domain;

class DomainFunctionsDataFixture extends AbstractNativeFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$this->createDomainIdsByLocaleFunction();
		$this->createLocaleByDomainIdFunction();
	}

	private function createDomainIdsByLocaleFunction() {
		$domain = $this->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Component\Domain\Domain */

		$domainsIdsByLocale = [];
		foreach ($domain->getAll() as $domainConfig) {
			$domainsIdsByLocale[$domainConfig->getLocale()][] = $domainConfig->getId();
		}

		$domainIdsByLocaleSqlClauses = [];
		foreach ($domainsIdsByLocale as $locale => $domainIds) {
			$domainIdsByLocaleSqlClauses[] =
				'WHEN locale = \'' . $locale . '\' THEN RETURN QUERY VALUES (' . implode(', ', $domainIds) . ');';
		}

		$this->executeNativeQuery('
			CREATE OR REPLACE FUNCTION get_domain_ids_by_locale(locale text) RETURNS TABLE(domain_id integer)  AS $$
			BEGIN
				CASE
					' . implode("\n", $domainIdsByLocaleSqlClauses) . '
					ELSE RAISE EXCEPTION \'Locale % does not exists\', locale;
				END CASE;
			END
			$$ LANGUAGE plpgsql IMMUTABLE;
		');
	}

	private function createLocaleByDomainIdFunction() {
		$domain = $this->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Component\Domain\Domain */

		$localeByDomainIdSqlClauses = [];
		foreach ($domain->getAll() as $domainConfig) {
			$localeByDomainIdSqlClauses[] =
				'WHEN domain_id = ' . $domainConfig->getId()
				. ' THEN RETURN \'' . $domainConfig->getLocale() . '\';';
		}

		$this->executeNativeQuery('
			CREATE OR REPLACE FUNCTION get_domain_locale(domain_id integer) RETURNS text AS $$
			BEGIN
				CASE
					' . implode("\n", $localeByDomainIdSqlClauses) . '
					ELSE RAISE EXCEPTION \'Domain with ID % does not exists\', domain_id;
				END CASE;
			END
			$$ LANGUAGE plpgsql IMMUTABLE;
		');
	}

}
