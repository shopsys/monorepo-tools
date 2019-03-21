<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\ReleaseCandidate;

use PharIo\Version\Version;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;
use Symfony\Component\Console\Style\SymfonyStyle;

final class DumpTranslationsReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @param \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle
     */
    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
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
        $this->processRunner->run('php phing dump-translations');

        if ($this->hasNewTranslations()) {
            if ($this->hasOnlyDeletedFiles()) {
                $this->commit('dump translations');
                $this->symfonyStyle->success('Translations were dumped and only deleted were found and committed');
            } else {
                $this->symfonyStyle->note('There are new translations, check the changed files (you can use "git status") command, fill in the missing translations and commit the changes');
                $this->confirm('Confirm files are checked, missing translations completed and the changes are committed');
            }
        } else {
            $this->symfonyStyle->success('There are no new translations');
        }
    }

    /**
     * @return bool
     */
    private function hasNewTranslations(): bool
    {
        return !$this->isGitWorkingTreeEmpty();
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE_CANDIDATE;
    }

    /**
     * @return bool
     */
    private function hasOnlyDeletedFiles(): bool
    {
        $allFilesStatus = $this->getProcessResult(['git', 'status', '-s']);
        $allFilesCount = $this->countFilesInStatus($allFilesStatus);

        $deletedFilesStatus = $this->getProcessResult(['git', 'ls-files', '-d']);
        $deletedFilesCount = $this->countFilesInStatus($deletedFilesStatus);

        // has only deleted files
        if ($deletedFilesCount === $allFilesCount) {
            return true;
        }

        // has also some modified or added files
        return false;
    }

    /**
     * @param string $filesStatus
     * @return int
     */
    private function countFilesInStatus(string $filesStatus): int
    {
        if (empty($filesStatus)) {
            return 0;
        }

        return substr_count($filesStatus, "\n") + 1;
    }
}
