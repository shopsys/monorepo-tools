<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker;

use Nette\Utils\Strings;
use PharIo\Version\Version;
use Shopsys\Releaser\Message;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

final class ValidateRequireFormatInComposerJsonReleaseWorker implements ReleaseWorkerInterface
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
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var bool
     */
    private $isSuccessful = false;

    /**
     * @var string[]
     */
    private $lockVersionAllowedPackages = [];

    /**
     * @param \Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider $composerJsonProvider
     * @param \Symplify\MonorepoBuilder\FileSystem\JsonFileManager $jsonFileManager
     * @param \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle
     * @param string[] $lockVersionAllowedPackages
     */
    public function __construct(
        ComposerJsonProvider $composerJsonProvider,
        JsonFileManager $jsonFileManager,
        SymfonyStyle $symfonyStyle,
        array $lockVersionAllowedPackages
    ) {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->jsonFileManager = $jsonFileManager;
        $this->symfonyStyle = $symfonyStyle;
        $this->lockVersionAllowedPackages = $lockVersionAllowedPackages;
    }

    /**
     * @return string
     */
    public function getDescription(): string
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
        foreach ($this->composerJsonProvider->getRootAndPackageFileInfos() as $fileInfo) {
            $jsonContent = $this->jsonFileManager->loadFromFileInfo($fileInfo);

            $this->validateVersions($jsonContent, 'require', $fileInfo);
            $this->validateVersions($jsonContent, 'require-dev', $fileInfo);
        }

        if ($this->isSuccessful) {
            $this->symfonyStyle->success(Message::SUCCESS);
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

        // skip shopsys packages mutual dependencies
        if (Strings::startsWith($packageName, 'shopsys')) {
            return true;
        }

        if (in_array($packageName, $this->lockVersionAllowedPackages, true)) {
            return true;
        }

        return false;
    }
}
