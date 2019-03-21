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
        return 'Checkout to master, create, and [Manually] push a git tag';
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
        $versionString = $version->getVersionString();
        $this->processRunner->run('git fetch --prune');
        $this->processRunner->run('git checkout origin/master');
        $this->processRunner->run('git tag ' . $versionString);
        $this->symfonyStyle->note(sprintf('You need to push tag manually using "git push origin %s" command.', $versionString));
        $this->symfonyStyle->note('Rest assured, after you push the tagged master branch, the new tag will be propagated to packagist once the project is built and split on Heimdall automatically.');

        $this->confirm(sprintf('Confirm that tag "%s" is pushed', $versionString));
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE;
    }
}
