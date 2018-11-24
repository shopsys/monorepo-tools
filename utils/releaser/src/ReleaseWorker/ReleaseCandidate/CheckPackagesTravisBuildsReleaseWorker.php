<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker;

use PharIo\Version\Version;
use Shopsys\Releaser\Stage;
use Shopsys\Releaser\Travis\TravisStatusReporter;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\StageAwareReleaseWorkerInterface;

/**
 * @see https://stackoverflow.com/questions/34277366/how-to-list-all-builds-of-a-given-project-through-travis-api
 * @see http://docs.guzzlephp.org/en/stable/quickstart.html#concurrent-requests
 */
final class CheckPackagesTravisBuildsReleaseWorker implements ReleaseWorkerInterface, StageAwareReleaseWorkerInterface
{
    /**
     * @var string
     */
    private const STATUS_SUCCESS = 'Success';

    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var \Shopsys\Releaser\Travis\TravisStatusReporter
     */
    private $travisStatusReporter;

    /**
     * @param \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle
     * @param \Shopsys\Releaser\Travis\TravisStatusReporter $travisStatusReporter
     */
    public function __construct(SymfonyStyle $symfonyStyle, TravisStatusReporter $travisStatusReporter)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->travisStatusReporter = $travisStatusReporter;
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
        $statusForPackages = $this->travisStatusReporter->getStatusForPackagesByOrganization('shopsys');

        foreach ($statusForPackages as $package => $status) {
            if ($status === self::STATUS_SUCCESS) {
                $this->symfonyStyle->note(sprintf('"%s" package is passing', $package));
            } else {
                $this->symfonyStyle->error(sprintf(
                    '"%s" package is failing. Go check why:%s%s',
                    $package,
                    PHP_EOL,
                    sprintf('https://travis-ci.org/%s/branches', $package)
                ));
            }
        }

        die;
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE_CANDIDATE;
    }
}
