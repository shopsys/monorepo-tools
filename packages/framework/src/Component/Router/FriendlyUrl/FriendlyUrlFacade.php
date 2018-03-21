<?php

namespace Shopsys\FrameworkBundle\Component\Router\FriendlyUrl;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory;
use Shopsys\FrameworkBundle\Form\FriendlyUrlType;
use Shopsys\FrameworkBundle\Form\UrlListData;

class FriendlyUrlFacade
{
    const MAX_URL_UNIQUE_RESOLVE_ATTEMPT = 100;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory
     */
    private $domainRouterFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlService
     */
    private $friendlyUrlService;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository
     */
    private $friendlyUrlRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(
        EntityManagerInterface $em,
        DomainRouterFactory $domainRouterFactory,
        FriendlyUrlService $friendlyUrlService,
        FriendlyUrlRepository $friendlyUrlRepository,
        Domain $domain
    ) {
        $this->em = $em;
        $this->domainRouterFactory = $domainRouterFactory;
        $this->friendlyUrlService = $friendlyUrlService;
        $this->friendlyUrlRepository = $friendlyUrlRepository;
        $this->domain = $domain;
    }

    /**
     * @param string $routeName
     * @param int $entityId
     * @param string[] $namesByLocale
     */
    public function createFriendlyUrls($routeName, $entityId, array $namesByLocale)
    {
        $friendlyUrls = $this->friendlyUrlService->createFriendlyUrls($routeName, $entityId, $namesByLocale);
        foreach ($friendlyUrls as $friendlyUrl) {
            $locale = $this->domain->getDomainConfigById($friendlyUrl->getDomainId())->getLocale();
            $this->resolveUniquenessOfFriendlyUrlAndFlush($friendlyUrl, $namesByLocale[$locale]);
        }
    }

    /**
     * @param string $routeName
     * @param int $entityId
     * @param string $entityName
     * @param int $domainId
     */
    public function createFriendlyUrlForDomain($routeName, $entityId, $entityName, $domainId)
    {
        $friendlyUrl = $this->friendlyUrlService->createFriendlyUrlIfValid($routeName, $entityId, $entityName, $domainId);
        if ($friendlyUrl !== null) {
            $this->resolveUniquenessOfFriendlyUrlAndFlush($friendlyUrl, $entityName);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl $friendlyUrl
     * @param string $entityName
     */
    private function resolveUniquenessOfFriendlyUrlAndFlush(FriendlyUrl $friendlyUrl, $entityName)
    {
        $attempt = 0;
        do {
            $attempt++;
            if ($attempt > self::MAX_URL_UNIQUE_RESOLVE_ATTEMPT) {
                throw new \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\Exception\ReachMaxUrlUniqueResolveAttemptException(
                    $friendlyUrl,
                    $attempt
                );
            }

            $domainRouter = $this->domainRouterFactory->getRouter($friendlyUrl->getDomainId());
            try {
                $matchedRouteData = $domainRouter->match('/' . $friendlyUrl->getSlug());
            } catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {
                $matchedRouteData = null;
            }

            $friendlyUrlUniqueResult = $this->friendlyUrlService->getFriendlyUrlUniqueResult(
                $attempt,
                $friendlyUrl,
                $entityName,
                $matchedRouteData
            );
            $friendlyUrl = $friendlyUrlUniqueResult->getFriendlyUrlForPersist();
        } while (!$friendlyUrlUniqueResult->isUnique());

        if ($friendlyUrl !== null) {
            $this->em->persist($friendlyUrl);
            $this->em->flush($friendlyUrl);
            $this->setFriendlyUrlAsMain($friendlyUrl);
        }
    }

    /**
     * @param string $routeName
     * @param int $entityId
     * @return \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl[]
     */
    public function getAllByRouteNameAndEntityId($routeName, $entityId)
    {
        return $this->friendlyUrlRepository->getAllByRouteNameAndEntityId($routeName, $entityId);
    }

    /**
     * @param int $domainId
     * @param string $routeName
     * @param int $entityId
     * @return \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl|null
     */
    public function findMainFriendlyUrl($domainId, $routeName, $entityId)
    {
        return $this->friendlyUrlRepository->findMainFriendlyUrl($domainId, $routeName, $entityId);
    }

    /**
     * @param string $routeName
     * @param int $entityId
     * @param \Shopsys\FrameworkBundle\Form\UrlListData $urlListData
     */
    public function saveUrlListFormData($routeName, $entityId, UrlListData $urlListData)
    {
        $toFlush = [];

        foreach ($urlListData->mainFriendlyUrlsByDomainId as $friendlyUrl) {
            if ($friendlyUrl !== null) {
                $this->setFriendlyUrlAsMain($friendlyUrl);
                $toFlush[] = $friendlyUrl;
            }
        }

        foreach ($urlListData->toDelete as $friendlyUrls) {
            foreach ($friendlyUrls as $friendlyUrl) {
                if (!$friendlyUrl->isMain()) {
                    $this->em->remove($friendlyUrl);
                }
            }
        }

        foreach ($urlListData->newUrls as $urlData) {
            $domainId = $urlData[FriendlyUrlType::FIELD_DOMAIN];
            $newSlug = $urlData[FriendlyUrlType::FIELD_SLUG];
            $newFriendlyUrl = new FriendlyUrl($routeName, $entityId, $domainId, $newSlug);
            $this->em->persist($newFriendlyUrl);
            $toFlush[] = $newFriendlyUrl;
        }

        $this->em->flush($toFlush);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl $mainFriendlyUrl
     */
    private function setFriendlyUrlAsMain(FriendlyUrl $mainFriendlyUrl)
    {
        $friendlyUrls = $this->friendlyUrlRepository->getAllByRouteNameAndEntityIdAndDomainId(
            $mainFriendlyUrl->getRouteName(),
            $mainFriendlyUrl->getEntityId(),
            $mainFriendlyUrl->getDomainId()
        );
        foreach ($friendlyUrls as $friendlyUrl) {
            $friendlyUrl->setMain(false);
        }
        $mainFriendlyUrl->setMain(true);

        $this->em->flush($friendlyUrls);
    }
}
