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
     * @param \Symfony\Component\Finder\SplFileInfo $splFileInfo
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function processFileToString(SplFileInfo $splFileInfo, Version $version): string
    {
        $content = $this->cleanFromPlaceholders($splFileInfo->getContents());

        $content = $this->changeUnreleasedHeadlineToVersionAndDate($version, $content);

        return $this->updateFooterLinks($version, $content);
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
     * @param string $changelogContent
     * @return string
     */
    private function changeUnreleasedHeadlineToVersionAndDate(Version $version, string $changelogContent): string
    {
        $newHeadline = '## ' . $version->getVersionString() . ' - ' . (new DateTime())->format('Y-m-d');

        return Strings::replace($changelogContent, '#\#\# Unreleased#', $newHeadline);
    }

    /**
     * @see https://regex101.com/r/u8mr0w/1
     * @param \PharIo\Version\Version $version
     * @param string $content
     * @return string
     */
    private function updateFooterLinks(Version $version, string $content): string
    {
        return Strings::replace(
            $content,
            '#^(\[)Unreleased(\]: .*?)HEAD#m',
            sprintf('$1%s$2%s', $version->getVersionString(), $version->getVersionString())
        );
    }
}
