<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\ReleaseCandidate;

use Nette\Utils\FileSystem;
use PharIo\Version\Version;
use Shopsys\Releaser\FileManipulator\FrameworkBundleVersionFileManipulator;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;
use Symplify\MonorepoBuilder\Release\Message;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class SetFrameworkBundleVersionReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @var \Shopsys\Releaser\FileManipulator\FrameworkBundleVersionFileManipulator
     */
    private $frameworkBundleVersionFileManipulator;

    /**
     * @param \Shopsys\Releaser\FileManipulator\FrameworkBundleVersionFileManipulator $frameworkBundleVersionFileManipulator
     */
    public function __construct(
        FrameworkBundleVersionFileManipulator $frameworkBundleVersionFileManipulator
    ) {
        $this->frameworkBundleVersionFileManipulator = $frameworkBundleVersionFileManipulator;
    }

    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return 'Set ShopsysFrameworkBundle version to released version and commit it.';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 845;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        $this->updateFrameworkBundleVersion($version);

        $this->commit(sprintf(
            'ShopsysFrameworkBundle: version updated to "%s"',
            $version->getVersionString()
        ));

        $this->symfonyStyle->success(Message::SUCCESS);
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
    private function updateFrameworkBundleVersion(Version $version): void
    {
        $upgradeFilePath = getcwd() . FrameworkBundleVersionFileManipulator::FRAMEWORK_BUNDLE_VERSION_FILE_PATH;
        $upgradeFileInfo = new SmartFileInfo($upgradeFilePath);

        $newUpgradeContent = $this->frameworkBundleVersionFileManipulator->updateFrameworkBundleVersion($upgradeFileInfo, $version);

        FileSystem::write($upgradeFilePath, $newUpgradeContent);
    }
}
