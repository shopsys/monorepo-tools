<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\AfterRelease;

use PharIo\Version\Version;
use Shopsys\Releaser\Packagist\PackageProvider;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;
use Symplify\MonorepoBuilder\Release\Message;

final class CheckPackagesOnPackagistReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @var \Shopsys\Releaser\Packagist\PackageProvider
     */
    private $packageProvider;

    /**
     * @param \Shopsys\Releaser\Packagist\PackageProvider $packageProvider
     */
    public function __construct(PackageProvider $packageProvider)
    {
        $this->packageProvider = $packageProvider;
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 280;
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
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return 'Check there are new versions all packages on packagist';
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        $packagesWithVersions = $this->packageProvider->getPackagesWithsVersionsByOrganization('shopsys');

        $packageWithoutVersion = [];
        $versionsAsString = $version->getVersionString();
        foreach ($packagesWithVersions as $package => $packageVersions) {
            if (in_array($versionsAsString, $packageVersions, true)) {
                continue;
            }

            $packageWithoutVersion[] = $package;
        }

        if (count($packageWithoutVersion)) {
            $this->symfonyStyle->error(sprintf('Some packages on packagist do not have "%s" version', $versionsAsString));
            $this->symfonyStyle->listing($packageWithoutVersion);

            $this->confirm('Confirm the missing versions are fixed');
        } else {
            $this->symfonyStyle->success(Message::SUCCESS);
        }
    }
}
