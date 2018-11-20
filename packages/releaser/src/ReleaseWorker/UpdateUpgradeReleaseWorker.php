<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker;

use Nette\Utils\FileSystem;
use PharIo\Version\Version;
use Shopsys\Releaser\FileManipulator\UpgradeFileManipulator;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

final class UpdateUpgradeReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var \Shopsys\Releaser\FileManipulator\UpgradeFileManipulator
     */
    private $upgradeFileManipulator;

    /**
     * @param \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle
     * @param \Shopsys\Releaser\FileManipulator\UpgradeFileManipulator $upgradeFileManipulator
     */
    public function __construct(SymfonyStyle $symfonyStyle, UpgradeFileManipulator $upgradeFileManipulator)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->upgradeFileManipulator = $upgradeFileManipulator;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Update UPGRADE.md from/to headline with new version';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 800;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        return;

        // load
        $changelogFilePath = getcwd() . '/UPGRADE.md';

        // change
        $newChangelogContent = $this->upgradeFileManipulator->processFileToString($changelogFilePath, $version);

        // save
        FileSystem::write($changelogFilePath, $newChangelogContent);

        $this->symfonyStyle->success(sprintf('Headlines of "%s" file were updated', realpath($changelogFilePath)));
    }
}
