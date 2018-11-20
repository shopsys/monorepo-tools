<?php

declare(strict_types=1);

namespace Shopsys\Releaser\Tests\FileManipulator\UpgradeFileManipulator;

use PharIo\Version\Version;
use PHPUnit\Framework\TestCase;
use Shopsys\Releaser\FileManipulator\UpgradeFileManipulator;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class UpgradeFileManipulatorTest extends TestCase
{
    /**
     * @var \Shopsys\Releaser\FileManipulator\UpgradeFileManipulator
     */
    private $upgradeFileManipulator;

    protected function setUp()
    {
        $this->upgradeFileManipulator = new UpgradeFileManipulator('shopsys/shopsys');
    }

    public function test(): void
    {
        $changedContent = $this->upgradeFileManipulator->processFileToString(
            new SmartFileInfo(__DIR__ . '/Source/UPGRADE-before.md'),
            new Version('v1.0.0')
        );

        $this->assertStringMatchesFormatFile(__DIR__ . '/Source/UPGRADE-after.md', $changedContent);
    }

    public function testAlreadyDone(): void
    {
        $changedContent = $this->upgradeFileManipulator->processFileToString(
            new SmartFileInfo(__DIR__ . '/Source/UPGRADE-after.md'),
            new Version('v1.0.0')
        );

        $this->assertStringMatchesFormatFile(__DIR__ . '/Source/UPGRADE-after.md', $changedContent);
    }
}
