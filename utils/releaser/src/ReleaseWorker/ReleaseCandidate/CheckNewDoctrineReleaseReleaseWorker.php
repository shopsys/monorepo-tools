<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker;

use Nette\Utils\Strings;
use PharIo\Version\Version;
use Shopsys\Releaser\Guzzle\ApiCaller;
use Shopsys\Releaser\Stage;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\StageAwareReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Message;

final class CheckNewDoctrineReleaseReleaseWorker implements ReleaseWorkerInterface, StageAwareReleaseWorkerInterface
{
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var \Shopsys\Releaser\Guzzle\ApiCaller
     */
    private $apiCaller;

    /**
     * @var string
     */
    private const FORKED_DOCTINE = 'shopsys/doctrine-orm';

    /**
     * @var string
     */
    private const ORIGIN_DOCTINE = 'doctrine/orm';

    /**
     * @param \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle
     * @param \Shopsys\Releaser\Guzzle\ApiCaller $apiCaller
     */
    public function __construct(SymfonyStyle $symfonyStyle, ApiCaller $apiCaller)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->apiCaller = $apiCaller;
    }

    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return sprintf(
            'Check new release of "%s" package and propose upgrade of our fork ("%s") if necessary',
            self::ORIGIN_DOCTINE,
            self::FORKED_DOCTINE
        );
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 880;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        return;

        $forkedDoctrineVersion = $this->getMostRecentStableVersionForPackage(self::FORKED_DOCTINE);
        $originDoctrineVersion = $this->getMostRecentStableVersionForPackage(self::ORIGIN_DOCTINE);

        if ($forkedDoctrineVersion === $originDoctrineVersion) {
            $this->symfonyStyle->success(Message::SUCCESS);
            return;
        }

        $this->symfonyStyle->error(sprintf(
            'There is new version of "%s". Update the fork "%s" and release new version for it."',
            self::ORIGIN_DOCTINE,
            self::FORKED_DOCTINE
        ));
    }

    /**
     * @param string $packageName
     */
    private function getMostRecentStableVersionForPackage(string $packageName)
    {
        $url = sprintf('https://repo.packagist.org/p/%s.json', $packageName);
        $json = $this->apiCaller->sendGetToJsonArray($url);

        if (!isset($json['packages'][$packageName])) {
            return null;
        }

        $versions = array_keys($json['packages'][$packageName]);
        rsort($versions);

        foreach ($versions as $version) {
            if (Strings::match($version, '#(.*?)dev(.*?)#')) {
                continue;
            }

            return $version;
        }

        return null;
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE_CANDIDATE;
    }
}
