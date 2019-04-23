<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\AfterRelease;

use PharIo\Version\Version;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;

final class CreateReleaseNotesReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return '[Manually] Create release notes for the new release on Github (link the changelog here)';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 140;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        $this->symfonyStyle->note('See https://github.com/shopsys/shopsys/releases. Use "Draft a new release" button for creating a new release. If you are not sure about the release name or description, you can get inspired by the previous releases.');
        $this->confirm('Confirm release notes are published on Github');
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::AFTER_RELEASE;
    }
}
