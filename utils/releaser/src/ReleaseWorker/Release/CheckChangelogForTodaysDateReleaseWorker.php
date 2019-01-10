<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\Release;

use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use PharIo\Version\Version;
use Shopsys\Releaser\FileManipulator\ChangelogFileManipulator;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;
use Symplify\MonorepoBuilder\Release\Message;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class CheckChangelogForTodaysDateReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @var \Shopsys\Releaser\FileManipulator\ChangelogFileManipulator
     */
    private $changelogFileManipulator;

    /**
     * @param \Shopsys\Releaser\FileManipulator\ChangelogFileManipulator $changelogFileManipulator
     */
    public function __construct(ChangelogFileManipulator $changelogFileManipulator)
    {
        $this->changelogFileManipulator = $changelogFileManipulator;
    }

    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return sprintf(
            'Check the release date of "%s" version is "%s" in CHANGELOG.md',
            $version->getVersionString(),
            $this->getTodayAsString()
        );
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 660;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        $changelogFilePath = getcwd() . '/CHANGELOG.md';
        $smartFileInfo = new SmartFileInfo($changelogFilePath);
        $fileContent = $smartFileInfo->getContents();

        $todayInString = $this->getTodayAsString();

        /**
         * @see https://regex101.com/r/izBgtv/6
         */
        $pattern = '#\#\# \[' . preg_quote($version->getVersionString()) . '\]\(.*\) - (\d+-\d+-\d+)#';
        $match = Strings::match($fileContent, $pattern);
        if ($match === null) {
            $this->symfonyStyle->error('Unable to find current release headline. You need to check the release date in CHANGELOG.md manually.');
            $this->confirm('Confirm you have manually checked the release date in CHANGELOG.md');
        }
        if ($todayInString !== $match[1]) {
            $newChangelogContent = $this->changelogFileManipulator->updateReleaseDateOfCurrentReleaseToToday($fileContent, $pattern, $todayInString);
            FileSystem::write($changelogFilePath, $newChangelogContent);

            $this->symfonyStyle->note(sprintf(
                'CHANGELOG.md date for "%s" version was updated to "%s".',
                $version->getVersionString(),
                $todayInString
            ));

            $this->commit('CHANGELOG.md date updated to today');
        }
        $this->symfonyStyle->success(Message::SUCCESS);
    }

    /**
     * @return string
     */
    private function getTodayAsString(): string
    {
        return (new DateTime())->format('Y-m-d');
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE;
    }
}
