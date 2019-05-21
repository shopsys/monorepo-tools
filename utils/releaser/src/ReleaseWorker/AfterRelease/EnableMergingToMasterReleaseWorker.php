<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\AfterRelease;

use PharIo\Version\Version;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;

final class EnableMergingToMasterReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return '[Manually] Enable merging to master';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 145;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        $this->symfonyStyle->note('Enable merging to master - let your colleagues know in "team_ssfw_devs" Slack channel, and erase the red cross from the "merge" column on the whiteboard in the office.');
        $this->confirm('Confirm merging to master is enabled.');
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::AFTER_RELEASE;
    }
}
