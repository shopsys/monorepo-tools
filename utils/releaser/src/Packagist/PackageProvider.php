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
     * @var string[][]
     */
    private $packagesByOrganization = [];

    /**
     * @param string $organization
     * @param string[] $excludePackages
     * @return string[]
     */
    public function getPackagesByOrganization(string $organization, array $excludePackages = []): array
    {
        if (isset($this->packagesByOrganization[$organization])) {
            return $this->packagesByOrganization[$organization];
        }

        $url = 'https://packagist.org/packages/list.json?vendor=' . $organization;

        $remoteContent = FileSystem::read($url);
        $json = Json::decode($remoteContent, Json::FORCE_ARRAY);

        $this->ensureIsValidResponse($json, $url);

        $packagesByOrganization = $this->filterOutExcludedPackages($json[self::PACKAGE_NAMES], $excludePackages);

        $this->packagesByOrganization[$organization] = $packagesByOrganization;

        return $packagesByOrganization;
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
