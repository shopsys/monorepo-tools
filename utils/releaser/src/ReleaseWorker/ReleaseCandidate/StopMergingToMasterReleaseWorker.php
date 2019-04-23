<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\ReleaseCandidate;

use PharIo\Version\Version;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;

final class StopMergingToMasterReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return '[Manually] Tell team to stop merging to `master` branch';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 940;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        $this->symfonyStyle->note('You need to write a warning message into "team_ssfw_devs" slack channel, as well as mark the "merge" column on the whiteboard in the office with a significant red cross along with "release in progress" note.');
        $this->confirm('Confirm the merging is stopped');
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE_CANDIDATE;
    }
}
