<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\AfterRelease;

use PharIo\Version\Version;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;

final class MeasurePerformanceReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return 'Measure the performance on performator';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 120;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        $this->symfonyStyle->note('See https://docs.google.com/document/d/1VRQOl_c2KkDekMUkLPwencUVhE3UPtvkQQSywNtjyX8/edit#heading=h.2h92hrp89r2b');
        $this->symfonyStyle->confirm('Confirm the performance test is finished');
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::AFTER_RELEASE;
    }
}
