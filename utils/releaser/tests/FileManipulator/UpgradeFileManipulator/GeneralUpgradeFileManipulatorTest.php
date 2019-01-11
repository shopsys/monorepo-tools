<?php

namespace Shopsys\Releaser\Tests\FileManipulator\UpgradeFileManipulator;

use PharIo\Version\Version;
use PHPUnit\Framework\TestCase;
use Shopsys\Releaser\FileManipulator\GeneralUpgradeFileManipulator;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

class GeneralUpgradeFileManipulatorTest extends TestCase
{
    /**
     * @var \Shopsys\Releaser\FileManipulator\GeneralUpgradeFileManipulator
     */
    private $generalUpgradeFileManipulator;

    protected function setUp(): void
    {
        $this->generalUpgradeFileManipulator = new GeneralUpgradeFileManipulator();
    }

    public function test(): void
    {
        $changedContent = $this->generalUpgradeFileManipulator->updateLinks(
            new SmartFileInfo(__DIR__ . '/Source/UPGRADE-general-before.md'),
            new Version('v7.0.0-beta5')
        );

        $this->assertStringMatchesFormatFile(__DIR__ . '/Source/UPGRADE-general-after.md', $changedContent);
    }
}
