<?php

declare(strict_types=1);

namespace Shopsys\Releaser\FileManipulator;

use Nette\Utils\Strings;
use PharIo\Version\Version;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @see https://github.com/shopsys/shopsys/pull/470/commits/f92e5fe531be771323ee142579117b47bfac1d4e
 */
final class UpgradeFileManipulator
{
    /**
     * @var string
     * @see https://regex101.com/r/cHAbva/1
     */
    private const FROM_TO_UNRELEASED_PATTERN = '#^(?<start>\#\# \[?From [\w.-]+ to )Unreleased(?<end>]?)$#m';

    /**
     * @var string
     * @see https://regex101.com/r/izBgtv/3
     */
    private const FROM_TO_UNRELEASED_LINK_PATTERN = '#^(?<start>\[From [\w.-]+ to )Unreleased(?<middle>.*?\.\.\.).*?\n#m';

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
     * @return string
     */
    public function processFileToString(SplFileInfo $splFileInfo, Version $version): string
    {
        $content = $this->updateHeadlines($version, $splFileInfo->getContents());

        return $this->updateFooterLinks($version, $content);
    }

    /**
     * Before:
     * ## [From v0.9.0 to Unreleased]
     *
     * After:
     * ## [From v1.0.0 to Unreleased]
     *
     * ## [From v0.9.0 to v1.0.0]
     *
     * @param \PharIo\Version\Version $version
     * @param string $content
     * @return string
     */
    private function updateHeadlines(Version $version, string $content): string
    {
        $newHeadline = $this->createNewHeadline($version);

        // already done
        if (Strings::contains($content, $newHeadline)) {
            return $content;
        }

        return Strings::replace(
            $content,
            self::FROM_TO_UNRELEASED_PATTERN,
            function ($match) use ($version, $newHeadline) {
                return $newHeadline . $match['start'] . $version->getVersionString() . $match['end'];
            }
        );
    }

    /**
     * Before:
     * [From v0.9.0 to Unreleased]: https://github.com/shopsys/shopsys/compare/v0.9.0...HEAD
     *
     * After:
     * [From v1.0.0 to Unreleased]: https://github.com/shopsys/shopsys/compare/v1.0.0...HEAD
     * [From v0.9.0 to v1.0.0]: https://github.com/shopsys/shopsys/compare/v0.9.0...v1.0.0
     *
     * @param \PharIo\Version\Version $version
     * @param string $content
     * @return string
     */
    private function updateFooterLinks(Version $version, string $content): string
    {
        $newFooterLink = $this->createNewFooterLink($version);

        // already done
        if (Strings::contains($content, $newFooterLink)) {
            return $content;
        }

        return Strings::replace(
            $content,
            self::FROM_TO_UNRELEASED_LINK_PATTERN,
            function (array $match) use ($newFooterLink, $version) {
                return $newFooterLink . $match['start'] . $version->getVersionString() . $match['middle'] . $version->getVersionString() . PHP_EOL;
            }
        );
    }

    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    private function createNewHeadline(Version $version): string
    {
        return sprintf('## [From %s to Unreleased]' . PHP_EOL . PHP_EOL, $version->getVersionString());
    }

    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    private function createNewFooterLink(Version $version): string
    {
        return sprintf(
            '[From %s to Unreleased]: https://github.com/%s/compare/%s...HEAD' . PHP_EOL,
            $version->getVersionString(),
            $this->monorepoPackageName,
            $version->getVersionString()
        );
    }
}
