<?php

declare(strict_types=1);

namespace Shopsys\Releaser\FileManipulator;

use Nette\Utils\Strings;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @see https://github.com/shopsys/shopsys/pull/470/commits/260b20f2fe1b92206a517caf3612fb08be74e1b5#diff-2ec0da887a1f97cc93349fc8df3ae2d7
 */
final class DockerComposeFileManipulator
{
    /**
     * @param \Symfony\Component\Finder\SplFileInfo $splFileInfo
     * @param string $oldVersion
     * @param string $newVersion
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function processFileToString(SplFileInfo $splFileInfo, string $oldVersion, string $newVersion): string
    {
        return Strings::replace(
            $splFileInfo->getContents(),
            '#(\s+image: shopsys\/microservice.*?:)' . preg_quote($oldVersion, '#') . '#',
            '$1' . $newVersion
        );
    }
}
