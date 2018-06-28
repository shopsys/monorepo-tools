<?php

namespace Tests\ShopBundle\Smoke;

use League\Flysystem\FilesystemInterface;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Feed\FeedFacade;
use Shopsys\FrameworkBundle\Model\Feed\FeedInfoInterface;
use Tests\ShopBundle\Test\FunctionalTestCase;

class AllFeedsTest extends FunctionalTestCase
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\FeedFacade
     */
    private $feedFacade;

    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    private $filesystem;

    protected function setUp()
    {
        $this->feedFacade = $this->getContainer()->get(FeedFacade::class);
        $this->filesystem = $this->getContainer()->get(FilesystemInterface::class);
    }

    /**
     * @return array
     */
    public function getAllFeedExportCreationData(): array
    {
        // Method setUp is called only before each test, data providers are called even before that
        $this->setUp();

        $domain = $this->getContainer()->get(Domain::class);
        /* @var \Shopsys\FrameworkBundle\Component\Domain\Domain $domain */

        $data = [];
        foreach ($this->feedFacade->getFeedsInfo() as $feedInfo) {
            foreach ($domain->getAll() as $domainConfig) {
                $key = sprintf('feed "%s" on domain "%s"', $feedInfo->getName(), $domainConfig->getName());
                $data[$key] = [$feedInfo, $domainConfig];
            }
        }

        return $data;
    }

    /**
     * @dataProvider getAllFeedExportCreationData
     *
     * @param \Shopsys\FrameworkBundle\Model\Feed\FeedInfoInterface $feedInfo
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     */
    public function testFeedIsExportable(FeedInfoInterface $feedInfo, DomainConfig $domainConfig): void
    {
        $this->cleanUp($feedInfo, $domainConfig);

        $this->feedFacade->generateFeed($feedInfo->getName(), $domainConfig);

        $feedFilepath = $this->feedFacade->getFeedFilepath($feedInfo, $domainConfig);
        $this->assertTrue($this->filesystem->has($feedFilepath), 'Exported feed file exists.');

        $this->cleanUp($feedInfo, $domainConfig);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Feed\FeedInfoInterface $feedInfo
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     */
    private function cleanUp(FeedInfoInterface $feedInfo, DomainConfig $domainConfig): void
    {
        $feedFilepath = $this->feedFacade->getFeedFilepath($feedInfo, $domainConfig);

        if ($this->filesystem->has($feedFilepath)) {
            $this->filesystem->delete($feedFilepath);
        }
    }
}
