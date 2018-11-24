<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\Release;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use PharIo\Version\Version;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;
use Symplify\MonorepoBuilder\Release\Message;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class CheckChangelogForTodaysDateReleaseWorker extends AbstractShopsysReleaseWorker
{
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
        $smartFileInfo = new SmartFileInfo(getcwd() . '/CHANGELOG.md');

        $fileContent = $smartFileInfo->getContents();

        $todayInString = $this->getTodayAsString();

        $pattern = '#\#\# ' . preg_quote($version->getVersionString(), '#') . ' - ' . $todayInString . '#';

        if (Strings::match($fileContent, $pattern)) {
            $this->symfonyStyle->success(Message::SUCCESS);
        } else {
            $this->symfonyStyle->error(sprintf('CHANGELOG.md has old date for "%s" version, update it to "%s".', $version->getVersionString(), $todayInString));

            // @todo update automatically and commit
            // 'git commit -m "CHANGELOG.md date update to today" && git push'

            $this->symfonyStyle->confirm('Confirm the date was updated');
        }
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
