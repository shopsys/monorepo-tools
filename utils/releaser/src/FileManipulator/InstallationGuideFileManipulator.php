<?php

namespace Shopsys\Releaser\FileManipulator;

use Nette\Utils\Strings;
use PharIo\Version\Version;
use Symfony\Component\Finder\SplFileInfo;

class InstallationGuideFileManipulator
{
    /**
     * @var string
     */
    private const UNRELEASED_NOTE_REGEX = '#^\*{2}This guide is for the version which is not released yet\. See the \[version for `(.*)`\]\(https:\/\/github\.com\/shopsys\/shopsys\/blob\/(.*)\/docs\/installation\/.*\.md\)\.\*{2}$#m';

    /**
     * @var string
     */
    private const UNRELEASED_NOTE_FORMAT = '**This guide is for the version which is not released yet. See the [version for `%s`](https://github.com/shopsys/shopsys/blob/%s/docs/installation/%s).**';

    /**
     * @var string
     */
    private const RELEASED_VERSION_NOTE_REGEX = '#^\*\*This guide is for version `.*`\. Switch to another tag to see other versions.\*\*$#m';

    /**
     * @var string
     */
    private const RELEASED_VERSION_NOTE_FORMAT = '**This guide is for version `%s`. Switch to another tag to see other versions.**';

    /**
     * From:
     * **This guide is for version `v7.0.0-beta5`. Switch to another tag to see other versions.**
     *
     * To:
     * **This guide is for the version which is not released yet. See the [version for `v7.0.0-beta5`](https://github.com/shopsys/shopsys/blob/v7.0.0-beta5/docs/installation/installation-using-docker-linux.md).**
     *
     * @param \Symfony\Component\Finder\SplFileInfo $splFileInfo
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function setUnreleasedNote(SplFileInfo $splFileInfo, Version $version)
    {
        $newUnreleasedNote = sprintf(
            self::UNRELEASED_NOTE_FORMAT,
            $version->getVersionString(),
            $version->getVersionString(),
            $splFileInfo->getFilename()
        );

        return Strings::replace(
            $splFileInfo->getContents(),
            self::RELEASED_VERSION_NOTE_REGEX,
            $newUnreleasedNote
        );
    }

    /**
     * From:
     * **This guide is for the version which is not released yet. See the [version for `v7.0.0-beta4`](https://github.com/shopsys/shopsys/blob/v7.0.0-beta4/docs/installation/installation-using-docker-linux.md).**
     *
     * To:
     * **This guide is for version `v7.0.0-beta5`. Switch to another tag to see other versions.**
     *
     * @param \Symfony\Component\Finder\SplFileInfo $splFileInfo
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function setReleasedVersionNote(SplFileInfo $splFileInfo, Version $version)
    {
        return Strings::replace(
            $splFileInfo->getContents(),
            self::UNRELEASED_NOTE_REGEX,
            sprintf(self::RELEASED_VERSION_NOTE_FORMAT, $version->getVersionString())
        );
    }
}
