<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\AfterRelease;

use Nette\Utils\FileSystem;
use PharIo\Version\Version;
use RuntimeException;
use Shopsys\Releaser\FileManipulator\FrameworkBundleVersionFileManipulator;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;
use Symfony\Component\Console\Question\Question;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class SetFrameworkBundleVersionToDevReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @var \Shopsys\Releaser\FileManipulator\FrameworkBundleVersionFileManipulator
     */
    private $frameworkBundleVersionFileManipulator;

    /**
     * @param \Shopsys\Releaser\FileManipulator\FrameworkBundleVersionFileManipulator $frameworkBundleVersionFileManipulator
     */
    public function __construct(FrameworkBundleVersionFileManipulator $frameworkBundleVersionFileManipulator)
    {
        $this->frameworkBundleVersionFileManipulator = $frameworkBundleVersionFileManipulator;
    }

    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return 'Set ShopsysFrameworkBundle version to next dev version and commit it.';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 170;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        $developmentVersion = $this->askForNextDevelopmentVersion($version);
        $this->updateFrameworkBundleVersion($developmentVersion);

        $this->commit(sprintf(
            'ShopsysFrameworkBundle: version updated to "%s"',
            $developmentVersion->getVersionString()
        ));

        $this->symfonyStyle->note('You need to push the master branch manually, however, you have to wait until the previous (tagged) master build is finished on Heimdall. Otherwise, master-project-base would have never been built from the source codes where there are dependencies on the tagged versions of shopsys packages.');
        $this->confirm('Confirm you have waited long enough and then pushed the master branch.');
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::AFTER_RELEASE;
    }

    /**
     * @param \PharIo\Version\Version $version
     * @return \PharIo\Version\Version
     */
    private function askForNextDevelopmentVersion(Version $version): Version
    {
        $suggestedDevelopmentVersion = $this->suggestDevelopmentVersion($version);

        $question = new Question('Enter next development version for ShopsysFrameworkBundle', $suggestedDevelopmentVersion->getVersionString());
        $question->setValidator(static function ($answer) {
            $version = new Version($answer);

            if (!$version->hasPreReleaseSuffix()) {
                throw new RuntimeException(
                    'Development version must be suffixed (with \'-dev\', \'-alpha1\', ...)'
                );
            }

            return $version;
        });

        return $this->symfonyStyle->askQuestion($question);
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

    /**
     * Return new development version (e.g. from 7.1.0 to 7.2.0-dev)
     * @param \PharIo\Version\Version $version
     * @return \PharIo\Version\Version
     */
    private function suggestDevelopmentVersion(Version $version): Version
    {
        $newVersionString = $version->getMajor()->getValue() . '.' . ($version->getMinor()->getValue() + 1) . '.0-dev';
        return new Version($newVersionString);
    }
}
