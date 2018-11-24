<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\ReleaseCandidate;

use Nette\Utils\FileSystem;
use PharIo\Version\Version;
use Shopsys\Releaser\FileManipulator\ChangelogFileManipulator;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class UpdateChangelogReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @var \Symplify\MonorepoBuilder\Release\Process\ProcessRunner
     */
    private $processRunner;

    /**
     * @var \Shopsys\Releaser\FileManipulator\ChangelogFileManipulator
     */
    private $changelogFileManipulator;

    /**
     * @param \Symplify\MonorepoBuilder\Release\Process\ProcessRunner $processRunner
     * @param \Shopsys\Releaser\FileManipulator\ChangelogFileManipulator $changelogFileManipulator
     */
    public function __construct(
        ProcessRunner $processRunner,
        ChangelogFileManipulator $changelogFileManipulator
    ) {
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
        $this->symfonyStyle->note('Dumping new items to CHANGELOG.md, this might take ~10 seconds');
        $this->processRunner->run('vendor/bin/changelog-linker dump-merges --in-packages --in-categories');

        // load
        $changelogFilePath = getcwd() . '/CHANGELOG.md';
        $changelogFileInfo = new SmartFileInfo($changelogFilePath);

        // change
        $newChangelogContent = $this->changelogFileManipulator->processFileToString($changelogFileInfo, $version);

        // save
        FileSystem::write($changelogFilePath, $newChangelogContent);

        $this->symfonyStyle->confirm('Confirm you have manually checked CHANGELOG.md and it is final');
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE_CANDIDATE;
    }
}
