<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\ReleaseCandidate;

use PharIo\Version\Version;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;
use Symplify\MonorepoBuilder\Release\Message;

final class TestYourBranchLocallyReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return 'Test your branch locally - running composer-dev, standards and tests - this might take a few minutes';
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
        try {
            $this->processRunner->run('php phing composer-dev standards tests', true);
        } catch (\Symfony\Component\Process\Exception\ProcessFailedException $ex) {
            $this->symfonyStyle->caution($ex->getProcess()->getOutput());
            $this->symfonyStyle->note('A problem occurred, check the output and fix it please.');
            $runChecksAgain = $this->symfonyStyle->ask('Run the checks again?', 'yes');
            if ($runChecksAgain === 'yes') {
                $this->work($version);
            }
        }
        $this->symfonyStyle->success(Message::SUCCESS);
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE_CANDIDATE;
    }
}
