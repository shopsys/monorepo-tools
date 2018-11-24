<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\ReleaseCandidate;

use Nette\Utils\Strings;
use PharIo\Version\Version;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\Package\PackageNamesProvider;
use Symplify\MonorepoBuilder\Release\Message;

final class ValidateRequireFormatInComposerJsonReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @var \Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var \Symplify\MonorepoBuilder\FileSystem\JsonFileManager
     */
    private $jsonFileManager;

    /**
     * @var bool
     */
    private $isSuccessful = false;

    /**
     * @var \Symplify\MonorepoBuilder\Package\PackageNamesProvider
     */
    private $packageNamesProvider;

    /**
     * @param \Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider $composerJsonProvider
     * @param \Symplify\MonorepoBuilder\FileSystem\JsonFileManager $jsonFileManager
     * @param \Symplify\MonorepoBuilder\Package\PackageNamesProvider $packageNamesProvider
     */
    public function __construct(
        ComposerJsonProvider $composerJsonProvider,
        JsonFileManager $jsonFileManager,
        PackageNamesProvider $packageNamesProvider
    ) {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->jsonFileManager = $jsonFileManager;
        $this->packageNamesProvider = $packageNamesProvider;
    }

    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return 'Validate "require" and "require-dev" version format for all packages';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 920;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        foreach ($this->composerJsonProvider->getRootAndPackageFileInfos() as $smartFileInfo) {
            $jsonContent = $this->jsonFileManager->loadFromFileInfo($smartFileInfo);

            $this->validateVersions($jsonContent, 'require', $smartFileInfo);
            $this->validateVersions($jsonContent, 'require-dev', $smartFileInfo);
        }

        if ($this->isSuccessful) {
            $this->symfonyStyle->success(Message::SUCCESS);
        } else {
            $this->symfonyStyle->confirm('Confirm all the requires are in the valid format');
        }
    }

    /**
     * @param mixed[] $jsonContent
     * @param string $section
     * @param \Symfony\Component\Finder\SplFileInfo $splFileInfo
     */
    private function validateVersions(array $jsonContent, string $section, SplFileInfo $splFileInfo): void
    {
        if (!isset($jsonContent[$section])) {
            return;
        }

        foreach ($jsonContent[$section] as $packageName => $version) {
            if ($this->shouldSkipPackageNameAndVersion($packageName, $version)) {
                continue;
            }

            $this->symfonyStyle->warning(sprintf(
                '"%s" file has invalid version format for "%s": "%s"',
                $splFileInfo->getPathname(),
                $packageName,
                $version
            ));

            $this->isSuccessful = false;
        }
    }

    /**
     * @param string $packageName
     * @param string $version
     * @return bool
     */
    private function shouldSkipPackageNameAndVersion(string $packageName, string $version): bool
    {
        if (Strings::startsWith($packageName, 'ext-')) {
            return true;
        }

        if (Strings::startsWith($version, '^')) {
            return true;
        }

        // skip shopsys packages mutual dependencies in monorepo
        if (in_array($packageName, $this->packageNamesProvider->provide(), true)) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE_CANDIDATE;
    }
}
