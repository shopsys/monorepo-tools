<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\ReleaseCandidate;

use PharIo\Version\Version;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;

final class TestYourBranchLocallyReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return 'Test your branch locally - running composer-dev, standards and tests"';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 750;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        $this->processRunner->run('php phing composer-dev');
        $this->processRunner->run('php phing standards');
        $this->processRunner->run('php phing tests');

        $this->symfonyStyle->confirm('Confirm standards and tests are passing');
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE_CANDIDATE;
    }
}
