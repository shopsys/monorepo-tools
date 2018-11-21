<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use PharIo\Version\Version;
use Shopsys\Releaser\Message;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
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
     * @return string
     */
    public function getDescription(): string
    {
        return 'Check the release date of the currently released version is today in CHANGELOG.md';
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

        $todayInString = (new DateTime())->format('Y-m-d');

        $pattern = '#\#\# ' . preg_quote($version->getVersionString(), '#') . ' - ' . $todayInString . '#';

        if (Strings::match($fileContent, $pattern)) {
            $this->symfonyStyle->success(Message::SUCCESS);
        } else {
            $this->symfonyStyle->error(sprintf('CHANGELOG.md has old date for "%s" version, update it to "%s".', $version->getVersionString(), $todayInString));
        }
    }
}
