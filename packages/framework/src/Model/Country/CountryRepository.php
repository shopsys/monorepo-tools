<?php

namespace Shopsys\FrameworkBundle\Model\Country;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Shopsys\FrameworkBundle\Component\Doctrine\SortableNullsWalker;
use Shopsys\FrameworkBundle\Model\Country\Exception\CountryNotFoundException;

class CountryRepository
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getCountryRepository()
    {
        return $this->em->getRepository(Country::class);
    }

    /**
     * @param string $locale
     * @param int $domainId
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createSortedJoinedQueryBuilder(string $locale, int $domainId): QueryBuilder
    {
        return $this->getCountryRepository()->createQueryBuilder('c')
            ->join(CountryDomain::class, 'cd', Join::WITH, 'c.id = cd.country AND cd.domainId = :domainId')
            ->join(CountryTranslation::class, 'ct', Join::WITH, 'c.id = ct.translatable AND ct.locale = :locale')
            ->orderBy('cd.priority', 'desc')
            ->addOrderBy('ct.name', 'asc')
            ->setParameter('locale', $locale)
            ->setParameter('domainId', $domainId);
    }

    /**
     * @param int $countryId
     * @return \Shopsys\FrameworkBundle\Model\Country\Country|null
     */
    public function findById($countryId): ?Country
    {
        return $this->getCountryRepository()->find($countryId);
    }

    /**
     * @param int $countryId
     * @return \Shopsys\FrameworkBundle\Model\Country\Country
     */
    public function getById($countryId): Country
    {
        $country = $this->findById($countryId);

        if ($country === null) {
            throw new CountryNotFoundException('Country with ID ' . $countryId . ' not found.');
        }

        return $country;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Country\Country[]
     */
    public function getAll(): array
    {
        return $this->getCountryRepository()->findAll();
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @return \Shopsys\FrameworkBundle\Model\Country\Country[]
     */
    public function getAllEnabledByDomainIdWithLocale(int $domainId, string $locale): array
    {
        return $this->createSortedJoinedQueryBuilder($locale, $domainId)
            ->where('cd.enabled = :enabled')
            ->setParameter('enabled', true)
            ->getQuery()
            ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, SortableNullsWalker::class)
            ->getResult();
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @return \Shopsys\FrameworkBundle\Model\Country\Country[]
     */
    public function getAllByDomainIdWithLocale(int $domainId, string $locale): array
    {
        return $this->createSortedJoinedQueryBuilder($locale, $domainId)
            ->getQuery()
            ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, SortableNullsWalker::class)
            ->getResult();
    }

    /**
     * @param string $countryCode
     * @return \Shopsys\FrameworkBundle\Model\Country\Country|null
     */
    public function findByCode(string $countryCode): ?Country
    {
        return $this->getCountryRepository()->findOneBy(['code' => $countryCode]);
    }
}
