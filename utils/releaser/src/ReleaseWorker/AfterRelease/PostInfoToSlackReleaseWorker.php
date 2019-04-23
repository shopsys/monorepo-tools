<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\AfterRelease;

use PharIo\Version\Version;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;

final class PostInfoToSlackReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return '[Manually] Post info to slack channels';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 80;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        $this->symfonyStyle->note('Add new posts to these channels: #news in public Slack, #group_ssfw_news in internal Slack. You do not need to write essays, just point out one or two most interesting changes and add links to the "release highlights" article and release notes notes on Github.');
        $this->confirm('Confirm the Slack is noted');
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::AFTER_RELEASE;
    }
}
