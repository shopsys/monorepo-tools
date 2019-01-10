<?php

declare(strict_types=1);

namespace Shopsys\Releaser\FileManipulator;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use PharIo\Version\Version;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @see https://github.com/shopsys/shopsys/pull/470/commits/71824561cf348e3c49a7b5b1842f23b451a3074d
 */
final class ChangelogFileManipulator
{
    /**
     * @var string
     */
    private $monorepoPackageName;

    /**
     * @param string $monorepoPackageName
     */
    public function __construct(string $monorepoPackageName)
    {
        $this->monorepoPackageName = $monorepoPackageName;
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $splFileInfo
     * @param \PharIo\Version\Version $version
     * @param \PharIo\Version\Version $mostRecentVersion
     * @return string
     */
    public function processFileToString(SplFileInfo $splFileInfo, Version $version, Version $mostRecentVersion): string
    {
        $content = $this->cleanFromPlaceholders($splFileInfo->getContents());

        return $this->changeUnreleasedHeadlineToVersionAndDate($version, $mostRecentVersion, $content);
    }

    /**
     * @param string $fileContent
     * @param string $currentReleaseHeadlinePattern
     * @param string $todayInString
     * @return string
     */
    public function updateReleaseDateOfCurrentReleaseToToday(string $fileContent, string $currentReleaseHeadlinePattern, string $todayInString): string
    {
        return Strings::replace(
            $fileContent,
            $currentReleaseHeadlinePattern,
            function ($match) use ($todayInString) {
                return str_replace($match[1], $todayInString, $match[0]);
            }
        );
    }

    /**
     * @param string $changelogContent
     * @return string
     */
    private function cleanFromPlaceholders(string $changelogContent): string
    {
        return Strings::replace($changelogContent, '#<\!-- dumped content (start|end) -->\n\n#');
    }

    /**
     * @param \PharIo\Version\Version $version
     * @param \PharIo\Version\Version $mostRecentVersion
     * @param string $changelogContent
     * @return string
     */
    private function changeUnreleasedHeadlineToVersionAndDate(Version $version, Version $mostRecentVersion, string $changelogContent): string
    {
        $newHeadline = '## ' . $this->createLink($version, $mostRecentVersion) . ' - ' . (new DateTime())->format('Y-m-d');

        return Strings::replace($changelogContent, '#\#\# Unreleased#', $newHeadline);
    }

    /**
     * @param \PharIo\Version\Version $version
     * @param \PharIo\Version\Version $mostRecentVersion
     * @return string
     */
    private function createLink(Version $version, Version $mostRecentVersion)
    {
        return sprintf(
            '[%s](https://github.com/%s/compare/%s...%s)',
            $version->getVersionString(),
            $this->monorepoPackageName,
            $mostRecentVersion->getVersionString(),
            $version->getVersionString()
        );
    }
}
