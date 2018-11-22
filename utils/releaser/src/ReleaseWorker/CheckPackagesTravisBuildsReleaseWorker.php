<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker;

use PharIo\Version\Version;
use Safe\simplexml_load_string;
use Shopsys\Releaser\Guzzle\ApiCaller;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

/**
 * @see https://stackoverflow.com/questions/34277366/how-to-list-all-builds-of-a-given-project-through-travis-api
 * @see http://docs.guzzlephp.org/en/stable/quickstart.html#concurrent-requests
 */
final class CheckPackagesTravisBuildsReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var string[]
     */
    private $travisPackages = [];

    /**
     * @var string[]
     */
    private $failedPackages = [];

    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var string
     */
    private const SUCCESS_STATUS = 'Success';

    /**
     * @var \Shopsys\Releaser\Guzzle\ApiCaller
     */
    private $apiCaller;

    /**
     * @param \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle
     * @param string[] $travisPackages
     * @param \Shopsys\Releaser\Guzzle\ApiCaller $apiCaller
     */
    public function __construct(SymfonyStyle $symfonyStyle, array $travisPackages, ApiCaller $apiCaller)
    {
        $this->travisPackages = $travisPackages;
        $this->symfonyStyle = $symfonyStyle;
        $this->apiCaller = $apiCaller;
    }

    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return 'Check Travis build status for all packages';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 1000;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        // @todo for fast development
        return;

        $urls = [];
        foreach ($this->travisPackages as $travisPackage) {
            $urls[] = $this->createUrlWithMasterBranchStatusInXml($travisPackage);
        }

        $responses = $this->apiCaller->sendGetsAsyncToStrings($urls);

        foreach ($responses as $response) {
            $xmlResponse = simplexml_load_string($response);

            $projectXmlElements = $xmlResponse->xpath('Project');
            $projectXmlElement = $projectXmlElements[0];

            $status = (string)$projectXmlElement->attributes()->lastBuildStatus;
            $packageName = (string)$projectXmlElement->attributes()->name;

            if ($status !== self::SUCCESS_STATUS) {
                $this->failedPackages[] = $packageName;
            }
        }

        if ($this->failedPackages === []) {
            $this->symfonyStyle->success('All packages are passing!');
        } else {
            $this->symfonyStyle->error('Some packages are failing');
            $this->symfonyStyle->listing($this->failedPackages);
        }

        die;
    }

    /**
     * @param string $travisPackage
     * @return string
     */
    private function createUrlWithMasterBranchStatusInXml(string $travisPackage): string
    {
        return 'https://api.travis-ci.org/repos/' . $travisPackage . '/cc.xml?branch=master';
    }
}
