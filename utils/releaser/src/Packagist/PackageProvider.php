<?php

declare(strict_types=1);

namespace Shopsys\Releaser\Packagist;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Shopsys\Releaser\Exception\ShouldNotHappenException;

final class PackageProvider
{
    /**
     * @var string
     */
    private const PACKAGE_NAMES = 'packageNames';

    /**
     * @param string $organization
     * @param string[] $excludePackages
     * @return string[]
     */
    public function getPackagesByOrganization(string $organization, array $excludePackages = []): array
    {
        $url = 'https://packagist.org/packages/list.json?vendor=' . $organization;
        $remoteContent = FileSystem::read($url);
        $json = Json::decode($remoteContent, Json::FORCE_ARRAY);

        $this->ensureIsValidResponse($json, $url);

        return $this->filterOutExcludedPackages($json[self::PACKAGE_NAMES], $excludePackages);
    }

    /**
     * @param string $organization
     * @return mixed[]
     */
    public function getPackagesWithVersionsByOrganization(string $organization): array
    {
        $packages = $this->getPackagesByOrganization($organization);
        $packagesWithVersions = [];
        foreach ($packages as $package) {
            $packagesWithVersions[$package] = $this->getPackageVersions($package);
        }

        return $packagesWithVersions;
    }

    /**
     * @param array $json
     * @param string $url
     */
    private function ensureIsValidResponse(array $json, string $url)
    {
        if (isset($json[self::PACKAGE_NAMES])) {
            return;
        }

        throw new ShouldNotHappenException(
            'Packagist API failed to list package names for url request:' . PHP_EOL . $url
        );
    }

    /**
     * @param string $package
     * @return string[]
     */
    private function getPackageVersions(string $package): array
    {
        $url = 'https://repo.packagist.org/p/' . $package . '.json';
        $remoteContent = FileSystem::read($url);
        $json = Json::decode($remoteContent, Json::FORCE_ARRAY);

        if (!isset($json['packages'][$package])) {
            return [];
        }

        return array_keys($json['packages'][$package]);
    }

    /**
     * @param string[] $packages
     * @param string[] $excludePackages
     * @return string[]
     */
    private function filterOutExcludedPackages(array $packages, array $excludePackages): array
    {
        if ($excludePackages === []) {
            return $packages;
        }

        foreach ($packages as $key => $package) {
            if (in_array($package, $excludePackages, true)) {
                unset($packages[$key]);
            }
        }

        return $packages;
    }
}
