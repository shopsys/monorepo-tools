<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Message;

abstract class AbstractCheckUncommittedChangesReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return 'Check the repository for any uncommitted changes';
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        if (!$this->isGitWorkingTreeEmpty()) {
            $this->symfonyStyle->warning('There are some uncommitted changes in your repository (see the result of "git status" command), please resolve them before you continue with the release process.');
            $this->confirm('Confirm that you have resolved all uncommitted files and your working tree is empty now.');
        } else {
            $this->symfonyStyle->success(Message::SUCCESS);
        }
    }
}
