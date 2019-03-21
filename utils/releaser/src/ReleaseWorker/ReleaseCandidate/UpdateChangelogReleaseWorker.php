<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\ReleaseCandidate;

use Nette\Utils\FileSystem;
use PharIo\Version\Version;
use Shopsys\Releaser\FileManipulator\ChangelogFileManipulator;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;
use Symplify\MonorepoBuilder\Split\Git\GitManager;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class UpdateChangelogReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @var \Shopsys\Releaser\FileManipulator\ChangelogFileManipulator
     */
    private $changelogFileManipulator;

    /**
     * @var \Symplify\MonorepoBuilder\Split\Git\GitManager
     */
    private $gitManager;

    /**
     * @param \Shopsys\Releaser\FileManipulator\ChangelogFileManipulator $changelogFileManipulator
     * @param \Symplify\MonorepoBuilder\Split\Git\GitManager $gitManager
     */
    public function __construct(ChangelogFileManipulator $changelogFileManipulator, GitManager $gitManager)
    {
        $this->changelogFileManipulator = $changelogFileManipulator;
        $this->gitManager = $gitManager;
    }

    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return 'Dump new features to CHANGELOG.md, clean from placeholders and [Manually] check everything is ok';
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
        $this->symfonyStyle->note('It is necessary to set Github token before the changelog content is generated');
        $githubToken = $this->symfonyStyle->ask('Please enter no-scope Github token (https://github.com/settings/tokens/new)');

        $this->symfonyStyle->note('Dumping new items to CHANGELOG.md, this might take ~10 seconds');
        $this->processRunner->run(sprintf('GITHUB_TOKEN=%s vendor/bin/changelog-linker dump-merges --in-packages --in-categories', $githubToken), true);

        // load
        $changelogFilePath = getcwd() . '/CHANGELOG.md';
        $changelogFileInfo = new SmartFileInfo($changelogFilePath);

        // change
        $mostRecentVersion = new Version($this->gitManager->getMostRecentTag(getcwd()));
        $newChangelogContent = $this->changelogFileManipulator->processFileToString($changelogFileInfo, $version, $mostRecentVersion);

        // save
        FileSystem::write($changelogFilePath, $newChangelogContent);

        $this->symfonyStyle->note(sprintf('You need to review the file, resolve unclassified entries, remove uninteresting entries, and commit the changes manually with "changelog is now updated for %s release"', $version->getVersionString()));

        $this->confirm('Confirm you have checked CHANGELOG.md and the changes are committed.');
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE_CANDIDATE;
    }
}
