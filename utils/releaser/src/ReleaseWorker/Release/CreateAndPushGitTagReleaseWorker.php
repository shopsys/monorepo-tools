<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\Release;

use PharIo\Version\Version;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;

final class CreateAndPushGitTagReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return 'Create and [Manually] push a git tag';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 630;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        $this->processRunner->run('git tag ' . $version->getVersionString());

        $this->confirm(sprintf('Confirm that tag "%s" is pushed', $version->getVersionString()));
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE;
    }
}
