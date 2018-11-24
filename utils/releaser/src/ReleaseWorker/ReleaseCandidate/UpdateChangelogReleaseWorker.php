<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker;

use Nette\Utils\FileSystem;
use PharIo\Version\Version;
use Shopsys\Releaser\FileManipulator\ChangelogFileManipulator;
use Shopsys\Releaser\Stage;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\StageAwareReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;

final class UpdateChangelogReleaseWorker implements ReleaseWorkerInterface, StageAwareReleaseWorkerInterface
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
     * @var \Shopsys\Releaser\FileManipulator\ChangelogFileManipulator
     */
    private $changelogFileManipulator;

    /**
     * @param \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle
     * @param \Symplify\MonorepoBuilder\Release\Process\ProcessRunner $processRunner
     * @param \Shopsys\Releaser\FileManipulator\ChangelogFileManipulator $changelogFileManipulator
     */
    public function __construct(
        SymfonyStyle $symfonyStyle,
        ProcessRunner $processRunner,
        ChangelogFileManipulator $changelogFileManipulator
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->processRunner = $processRunner;
        $this->changelogFileManipulator = $changelogFileManipulator;
    }

    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return 'Dump new features to CHANGELOG.md, clean from placeholders and manually check everything is ok';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 820;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        return;

        $this->symfonyStyle->note('Dumping new items to CHANGELOG.md, this might take ~10 seconds');
        $this->processRunner->run('vendor/bin/changelog-linker dump-merges --in-packages --in-categories');

        // load
        $changelogFilePath = getcwd() . '/CHANGELOG.md';

        // change
        $newChangelogContent = $this->changelogFileManipulator->processFileToString($changelogFilePath, $version);

        // save
        FileSystem::write($changelogFilePath, $newChangelogContent);
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE_CANDIDATE;
    }
}
