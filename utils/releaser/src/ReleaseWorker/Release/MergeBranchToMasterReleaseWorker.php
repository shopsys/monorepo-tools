<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\Release;

use PharIo\Version\Version;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;

final class MergeBranchToMasterReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return '[Manually] Merge branch into master';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 650;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        $this->symfonyStyle->note('You need to create a merge commit, see https://github.com/shopsys/shopsys/blob/master/docs/contributing/merging-to-master-on-github.md for detailed instructions.');
        $this->confirm('Confirm the release branch was merged to master');
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE;
    }
}
