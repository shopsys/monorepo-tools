<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\Release;

use Shopsys\Releaser\ReleaseWorker\AbstractCheckUncommittedChangesReleaseWorker;
use Shopsys\Releaser\Stage;

final class CheckUncommittedChangesReleaseWorker extends AbstractCheckUncommittedChangesReleaseWorker
{
    /**
     * @return int
     */
    public function getPriority(): int
    {
        return 700;
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE;
    }
}
