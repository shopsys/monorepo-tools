<?php

declare(strict_types=1);

namespace Shopsys\Releaser\FileManipulator;

use Nette\Utils\Strings;
use PharIo\Version\Version;
use Symfony\Component\Finder\SplFileInfo;

class GeneralUpgradeFileManipulator
{
    private const FROM_PREVIOUS_TO_UNRELEASED_LINK_PATTERN = '#^\* \#\#\# \[From [\w.-]+ to Unreleased\]\(\.\/docs\/upgrade\/UPGRADE-unreleased\.md\)$#m';

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $splFileInfo
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function updateLinks(SplFileInfo $splFileInfo, Version $version): string
    {
        $newLink = sprintf('* ### [From %s to Unreleased](./docs/upgrade/UPGRADE-unreleased.md)' . PHP_EOL, $version->getVersionString());
        return Strings::replace(
            $splFileInfo->getContents(),
            self::FROM_PREVIOUS_TO_UNRELEASED_LINK_PATTERN,
            function ($match) use ($version, $newLink) {
                return $newLink . str_ireplace('unreleased', $version->getVersionString(), $match[0]);
            }
        );
    }
}
