<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker;

use PharIo\Version\Version;
use Shopsys\Releaser\Travis\TravisStatusReporter;

/**
 * @see https://stackoverflow.com/questions/34277366/how-to-list-all-builds-of-a-given-project-through-travis-api
 * @see http://docs.guzzlephp.org/en/stable/quickstart.html#concurrent-requests
 */
abstract class AbstractCheckPackagesTravisBuildsReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @var string
     */
    private const STATUS_SUCCESS = 'Success';

    /**
     * @var \Shopsys\Releaser\Travis\TravisStatusReporter
     */
    private $travisStatusReporter;

    /**
     * @param \Shopsys\Releaser\Travis\TravisStatusReporter $travisStatusReporter
     */
    public function __construct(TravisStatusReporter $travisStatusReporter)
    {
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
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        $statusForPackages = $this->travisStatusReporter->getStatusForPackagesByOrganization('shopsys');

        $isPassing = true;

        foreach ($statusForPackages as $package => $status) {
            if ($status === self::STATUS_SUCCESS) {
                $this->symfonyStyle->note(sprintf('"%s" package is passing', $package));
            } else {
                $isPassing = false;
                $this->symfonyStyle->error(sprintf(
                    '"%s" package is failing. Go check why:%s%s',
                    $package,
                    PHP_EOL,
                    sprintf('https://travis-ci.org/%s/branches', $package)
                ));
            }
        }

        if ($isPassing === false) {
            $this->symfonyStyle->confirm('Continue after packages are resolved');
        }
    }
}
