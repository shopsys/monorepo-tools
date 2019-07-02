<?php

namespace Shopsys\FrameworkBundle\Model\Article;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlData;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlDataFactoryInterface;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlDataProviderInterface;

class ArticleDetailFriendlyUrlDataProvider implements FriendlyUrlDataProviderInterface
{
    protected const ROUTE_NAME = 'front_article_detail';

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlDataFactoryInterface
     */
    protected $friendlyUrlDataFactory;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlDataFactoryInterface $friendlyUrlDataFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        FriendlyUrlDataFactoryInterface $friendlyUrlDataFactory
    ) {
        $this->em = $em;
        $this->friendlyUrlDataFactory = $friendlyUrlDataFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlData[]
     */
    public function getFriendlyUrlData(DomainConfig $domainConfig): array
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('a.id, a.name')
            ->distinct()
            ->from(Article::class, 'a')
            ->leftJoin(FriendlyUrl::class, 'f', Join::WITH, 'a.id = f.entityId AND f.routeName = :routeName AND f.domainId = a.domainId')
            ->setParameter('routeName', static::ROUTE_NAME)
            ->where('f.entityId IS NULL AND a.domainId = :domainId')
            ->setParameter('domainId', $domainConfig->getId());
        $scalarData = $queryBuilder->getQuery()->getScalarResult();

        $friendlyUrlsData = [];

        foreach ($scalarData as $data) {
            $friendlyUrlsData[] = $this->createFriendlyUrlData($data);
        }

        return $friendlyUrlsData;
    }

    /**
     * @return string
     */
    public function getRouteName(): string
    {
        return static::ROUTE_NAME;
    }

    /**
     * @internal This method will be inlined when its implementation will be able to be simplified
     *
     * @param array $data
     * @return \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlData
     */
    protected function createFriendlyUrlData(array $data): FriendlyUrlData
    {
        if (method_exists($this->friendlyUrlDataFactory, 'createFromIdAndName')) {
            return $this->friendlyUrlDataFactory->createFromIdAndName($data['id'], $data['name']);
        }

        $message = sprintf(
            'Creating instance of FriendlyUrlData directly in "%s" is deprecated since SSFW 7.3, implement "%s" instead',
            __CLASS__,
            get_class($this->friendlyUrlDataFactory) . '::createFromIdAndName()'
        );
        @trigger_error($message, E_USER_DEPRECATED);

        $friendlyUrlData = $this->friendlyUrlDataFactory->create();
        $friendlyUrlData->id = $data['id'];
        $friendlyUrlData->name = $data['name'];

        return $friendlyUrlData;
    }
}
