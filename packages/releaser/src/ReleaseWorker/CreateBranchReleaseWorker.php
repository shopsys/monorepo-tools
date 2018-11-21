<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

final class CreateBranchReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return '[Manual] Create branch "rc-<version-number>"';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 980;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
    }
}
