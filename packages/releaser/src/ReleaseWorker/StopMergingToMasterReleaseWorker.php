<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

final class StopMergingToMasterReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return '[Manual] Tell team to stop mergin to `master` branch';
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
    }
}
