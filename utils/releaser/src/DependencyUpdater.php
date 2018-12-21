<?php declare(strict_types=1);

namespace Shopsys\Releaser;

use Nette\Utils\Strings;
use Symplify\MonorepoBuilder\Composer\Section;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

/**
 * Copy-pasted from @see \Symplify\MonorepoBuilder\DependencyUpdater
 * We need to exclude dependency on shopsys/coding-standards in shopsys/http-smoke-testing from automated updating.
 */
class DependencyUpdater
{
    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    public function __construct(JsonFileManager $jsonFileManager)
    {
        $this->jsonFileManager = $jsonFileManager;
    }

    /**
     * @param SmartFileInfo[] $smartFileInfos
     * @param string[] $packageNames
     * @param string $version
     */
    public function updateFileInfosWithPackagesAndVersion(
        array $smartFileInfos,
        array $packageNames,
        string $version
    ): void {
        foreach ($smartFileInfos as $packageComposerFileInfo) {
            $json = $this->jsonFileManager->loadFromFileInfo($packageComposerFileInfo);

            $json = $this->processSectionWithPackages($json, $packageNames, $version, Section::REQUIRE, null);
            $json = $this->processSectionWithPackages($json, $packageNames, $version, Section::REQUIRE_DEV, $packageComposerFileInfo->getPathname());

            $this->jsonFileManager->saveJsonWithFileInfo($json, $packageComposerFileInfo);
        }
    }

    /**
     * @param mixed[] $json
     * @param string[] $packageNames
     * @param string $targetVersion
     * @param string $section
     * @param string|null $filepathname
     * @return mixed[]
     */
    private function processSectionWithPackages(
        array $json,
        array $packageNames,
        string $targetVersion,
        string $section,
        ?string $filepathname
    ): array {
        if (!isset($json[$section])) {
            return $json;
        }

        foreach (array_keys($json[$section]) as $packageName) {
            if (!in_array($packageName, $packageNames, true) ||
                $this->isDependencyOnCodingStandardsInHttpSmokeTestingPackage($packageName, $filepathname)
            ) {
                continue;
            }

            $json[$section][$packageName] = $targetVersion;
        }

        return $json;
    }

    /**
     * shopsys/http-smoke-testing package is dependent on obsolete version of shopsys/coding-standards,
     * therefore must be excluded from automated updating of mutual dependencies
     * @param string $packageName
     * @param string|null $filepathname
     * @return bool
     */
    public function isDependencyOnCodingStandardsInHttpSmokeTestingPackage(string $packageName, ?string $filepathname): bool
    {
        return strpos((string)$filepathname, 'http-smoke-testing') !== false && $packageName === 'shopsys/coding-standards';
    }

}
