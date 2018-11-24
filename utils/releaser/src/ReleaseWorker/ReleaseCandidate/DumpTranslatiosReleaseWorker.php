<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker;

use Nette\Utils\Strings;
use PharIo\Version\Version;
use Shopsys\Releaser\Stage;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;

final class DumpTranslatiosReleaseWorker implements ReleaseWorkerInterface, StageAwareReleaseWorkerInterface
{
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var \Symplify\MonorepoBuilder\Release\Process\ProcessRunner
     */
    private $processRunner;

    /**
     * @param \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle
     * @param \Symplify\MonorepoBuilder\Release\Process\ProcessRunner $processRunner
     */
    public function __construct(SymfonyStyle $symfonyStyle, ProcessRunner $processRunner)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->processRunner = $processRunner;
    }

    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return 'Dump new translations with "php phing dump-translations" and commit them';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 860;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        return;

        $this->processRunner->run('php phing dump-translations');

        if ($this->hasNewTranslations()) {
            $this->symfonyStyle->note('[Manual] There are new translations. Check files, complete missing ones and then commit');
        } else {
            $this->symfonyStyle->success('There are no new translations');
        }
    }

    /**
     * @return bool
     */
    private function hasNewTranslations(): bool
    {
        $status = $this->processRunner->run('git status');

        return !Strings::contains($status, 'nothing to commit');
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE_CANDIDATE;
    }
}
