<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\ReleaseCandidate;

use PharIo\Version\Version;
use Shopsys\Releaser\ReleaseWorker\AbstractCheckShopsysInstallReleaseWorker;
use Shopsys\Releaser\Stage;

final class CheckShopsysInstallReleaseWorker extends AbstractCheckShopsysInstallReleaseWorker
{
    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return '[Manually] Install Shopsys Framework (project-base) using an installation guide (using Docker on Mac or Windows)';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 738;
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE_CANDIDATE;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        $versionStringWithoutPrefix = ltrim($version->getVersionString(), 'v');
        $branchName = $this->createBranchName($version);

        $this->symfonyStyle->note(sprintf(
            'Instructions for installation:

git clone https://github.com/shopsys/project-base.git
git checkout %1$s

# in composer.json, change a version of all shopsys/* packages from "%2$s" to "dev-%1$s as %2$s"

# remove all docker containers
docker rm $(docker ps -a -q)

# remove all docker images
docker rmi --force $(docker images -q)

# install the application following the corresponding installation guide',
            $branchName,
            $versionStringWithoutPrefix
        ));
        parent::work($version);
    }
}
