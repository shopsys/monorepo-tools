<?php

namespace Shopsys\FrameworkBundle\Component\Router\FriendlyUrl;

class FriendlyUrlUniqueResultFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFactoryInterface
     */
    protected $friendlyUrlFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFactoryInterface $friendlyUrlFactory
     */
    public function __construct(FriendlyUrlFactoryInterface $friendlyUrlFactory)
    {
        $this->friendlyUrlFactory = $friendlyUrlFactory;
    }

    /**
     * @param int $attempt
     * @param \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl $friendlyUrl
     * @param string $entityName
     * @param array|null $matchedRouteData
     * @return \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlUniqueResult
     */
    public function create(
        int $attempt,
        FriendlyUrl $friendlyUrl,
        string $entityName,
        array $matchedRouteData = null
    ) {
        if ($matchedRouteData === null) {
            return new FriendlyUrlUniqueResult(true, $friendlyUrl);
        }

        if ($friendlyUrl->getRouteName() === $matchedRouteData['_route']
            && $friendlyUrl->getEntityId() === $matchedRouteData['id']
        ) {
            return new FriendlyUrlUniqueResult(true, null);
        }

        $newIndexedFriendlyUrl = $this->friendlyUrlFactory->createIfValid(
            $friendlyUrl->getRouteName(),
            $friendlyUrl->getEntityId(),
            (string)$entityName,
            $friendlyUrl->getDomainId(),
            $attempt + 1 // if URL is duplicate, try again with "url-2", "url-3" and so on
        );

        return new FriendlyUrlUniqueResult(false, $newIndexedFriendlyUrl);
    }
}
