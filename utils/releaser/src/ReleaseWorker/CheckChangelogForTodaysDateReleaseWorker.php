<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use PharIo\Version\Version;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Message;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class CheckChangelogForTodaysDateReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;

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
        }
    }

    /**
     * @return string
     */
    private function getTodayAsString(): string
    {
        return (new DateTime())->format('Y-m-d');
    }
}
