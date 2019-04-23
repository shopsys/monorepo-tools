<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\AfterRelease;

use PharIo\Version\Version;
use Shopsys\Releaser\DependencyUpdater;
use Shopsys\Releaser\FilesProvider\ComposerJsonFilesProvider;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;
use Symplify\MonorepoBuilder\Package\PackageNamesProvider;

final class SetMutualDependenciesToDevMasterReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @var \Shopsys\Releaser\FilesProvider\ComposerJsonFilesProvider
     */
    private $composerJsonFilesProvider;

    /**
     * @var \Shopsys\Releaser\DependencyUpdater
     */
    private $dependencyUpdater;

    /**
     * @var \Symplify\MonorepoBuilder\Package\PackageNamesProvider
     */
    private $packageNamesProvider;

    /**
     * @var string
     */
    private const DEV_MASTER = 'dev-master';

    /**
     * @param \Shopsys\Releaser\FilesProvider\ComposerJsonFilesProvider $composerJsonFilesProvider
     * @param \Shopsys\Releaser\DependencyUpdater $dependencyUpdater
     * @param \Symplify\MonorepoBuilder\Package\PackageNamesProvider $packageNamesProvider
     */
    public function __construct(ComposerJsonFilesProvider $composerJsonFilesProvider, DependencyUpdater $dependencyUpdater, PackageNamesProvider $packageNamesProvider)
    {
        $this->composerJsonFilesProvider = $composerJsonFilesProvider;
        $this->dependencyUpdater = $dependencyUpdater;
        $this->packageNamesProvider = $packageNamesProvider;
    }

    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return sprintf('Set mutual package dependencies to "%s" version', self::DEV_MASTER);
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 160;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        $this->dependencyUpdater->updateFileInfosWithPackagesAndVersion(
            $this->composerJsonFilesProvider->provideExcludingMonorepoComposerJson(),
            $this->packageNamesProvider->provide(),
            self::DEV_MASTER
        );

        $this->commit('composer.json in all packages now use latest shopsys version');
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
}
