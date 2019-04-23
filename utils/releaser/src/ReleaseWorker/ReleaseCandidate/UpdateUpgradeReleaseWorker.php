<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\ReleaseCandidate;

use Nette\Utils\FileSystem;
use PharIo\Version\Version;
use Shopsys\Releaser\FileManipulator\GeneralUpgradeFileManipulator;
use Shopsys\Releaser\FileManipulator\MonorepoUpgradeFileManipulator;
use Shopsys\Releaser\FileManipulator\VersionUpgradeFileManipulator;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;
use Symplify\MonorepoBuilder\Release\Message;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Twig_Environment;

final class UpdateUpgradeReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @var \Shopsys\Releaser\FileManipulator\MonorepoUpgradeFileManipulator
     */
    private $monorepoUpgradeFileManipulator;

    /**
     * @var \Shopsys\Releaser\FileManipulator\GeneralUpgradeFileManipulator
     */
    private $generalUpgradeFileManipulator;

    /**
     * @var \Shopsys\Releaser\FileManipulator\VersionUpgradeFileManipulator
     */
    private $versionUpgradeFileManipulator;

    /**
     * @var \Twig_Environment
     */
    private $twigEnvironment;

    /**
     * @param \Shopsys\Releaser\FileManipulator\MonorepoUpgradeFileManipulator $monorepoUpgradeFileManipulator
     * @param \Shopsys\Releaser\FileManipulator\GeneralUpgradeFileManipulator $generalUpgradeFileManipulator
     * @param \Shopsys\Releaser\FileManipulator\VersionUpgradeFileManipulator $versionUpgradeFileManipulator
     * @param \Twig_Environment $twigEnvironment
     */
    public function __construct(
        MonorepoUpgradeFileManipulator $monorepoUpgradeFileManipulator,
        GeneralUpgradeFileManipulator $generalUpgradeFileManipulator,
        VersionUpgradeFileManipulator $versionUpgradeFileManipulator,
        Twig_Environment $twigEnvironment
    ) {
        $this->monorepoUpgradeFileManipulator = $monorepoUpgradeFileManipulator;
        $this->generalUpgradeFileManipulator = $generalUpgradeFileManipulator;
        $this->versionUpgradeFileManipulator = $versionUpgradeFileManipulator;
        $this->twigEnvironment = $twigEnvironment;
    }

    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return 'Prepare all upgrading files for the release.';
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
        $this->updateUpgradeFileForMonorepo($version);
        $this->createUpgradeFileForNewVersionFromUnreleased($version);
        $this->createUpgradeFileForUnreleased($version);
        $this->updateGeneralUpgradeFile($version);

        $this->symfonyStyle->success(Message::SUCCESS);
        $this->symfonyStyle->note('Review all the upgrading files whether they satisfy our rules and guidelines, see https://github.com/shopsys/shopsys/blob/master/docs/contributing/guidelines-for-writing-upgrade.md.');
        $versionString = $version->getVersionString();
        $this->symfonyStyle->note(sprintf(
            'Typically, you need to:
            - check the correctness of the order of Shopsys packages and sections, 
            - check whether there are no duplicated instructions for modifying docker related files, 
            - change the links from master to the %1$s version in UPGRADE-%1$s.md file.',
            $versionString
        ));
        $this->symfonyStyle->note(sprintf('You need to commit the upgrade files manually with commit message "upgrade files are now updated for %s release" commit message.', $versionString));

        $this->confirm('Confirm all upgrading files are ready for the release and the changes are committed');
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE_CANDIDATE;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    private function updateUpgradeFileForMonorepo(Version $version)
    {
        $upgradeFilePath = getcwd() . '/docs/contributing/upgrading-monorepo.md';
        $upgradeFileInfo = new SmartFileInfo($upgradeFilePath);

        $newUpgradeContent = $this->monorepoUpgradeFileManipulator->processFileToString($upgradeFileInfo, $version);

        FileSystem::write($upgradeFilePath, $newUpgradeContent);
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    private function createUpgradeFileForNewVersionFromUnreleased(Version $version)
    {
        $upgradeFilePath = getcwd() . '/docs/upgrade/UPGRADE-unreleased.md';
        $upgradeFileInfo = new SmartFileInfo($upgradeFilePath);

        $newUpgradeContent = $this->versionUpgradeFileManipulator->processFileToString($upgradeFileInfo, $version);

        FileSystem::write($upgradeFilePath, $newUpgradeContent);
        FileSystem::rename($upgradeFilePath, getcwd() . '/docs/upgrade/UPGRADE-' . $version->getVersionString() . '.md');
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    private function updateGeneralUpgradeFile(Version $version)
    {
        $upgradeFilePath = getcwd() . '/UPGRADE.md';
        $upgradeFileInfo = new SmartFileInfo($upgradeFilePath);

        $newUpgradeContent = $this->generalUpgradeFileManipulator->updateLinks($upgradeFileInfo, $version);

        FileSystem::write($upgradeFilePath, $newUpgradeContent);
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    private function createUpgradeFileForUnreleased(Version $version)
    {
        $content = $this->twigEnvironment->render(
            'UPGRADE-unreleased.md.twig',
            [
                'versionString' => $version->getVersionString(),
            ]
        );
        FileSystem::write(getcwd() . '/docs/upgrade/UPGRADE-unreleased.md', $content);
    }
}
