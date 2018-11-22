<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

final class CheckReleaseBlogPostReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return '[Manual] Prepare "Release highlights" post on https://blog.shopsys.com';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 960;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
    }
}
