<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\AfterRelease;

use Shopsys\Releaser\ReleaseWorker\AbstractCheckPackagesTravisBuildsReleaseWorker;
use Shopsys\Releaser\Stage;

final class CheckPackagesTravisBuildsReleaseWorker extends AbstractCheckPackagesTravisBuildsReleaseWorker
{
    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 240;
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::AFTER_RELEASE;
    }
}
