<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\AfterRelease;

use PharIo\Version\Version;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\InterdependencyUpdater;
use Symplify\MonorepoBuilder\Package\PackageNamesProvider;

final class SetMutualDependenciesToDevMasterReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @var \Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var \Symplify\MonorepoBuilder\InterdependencyUpdater
     */
    private $interdependencyUpdater;

    /**
     * @var \Symplify\MonorepoBuilder\Package\PackageNamesProvider
     */
    private $packageNamesProvider;

    /**
     * @var string
     */
    private const DEV_MASTER = 'dev-master';

    /**
     * @param \Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider $composerJsonProvider
     * @param \Symplify\MonorepoBuilder\InterdependencyUpdater $interdependencyUpdater
     * @param \Symplify\MonorepoBuilder\Package\PackageNamesProvider $packageNamesProvider
     */
    public function __construct(ComposerJsonProvider $composerJsonProvider, InterdependencyUpdater $interdependencyUpdater, PackageNamesProvider $packageNamesProvider)
    {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->interdependencyUpdater = $interdependencyUpdater;
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
        $this->interdependencyUpdater->updateFileInfosWithPackagesAndVersion(
            $this->composerJsonProvider->getPackagesFileInfos(),
            $this->packageNamesProvider->provide(),
            self::DEV_MASTER
        );

        // @todo 'git commit -m "all shopsys Docker images are now used in latest version" && git push
        $this->symfonyStyle->confirm('Confirm the composer versions were committed');
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::AFTER_RELEASE;
    }
}
