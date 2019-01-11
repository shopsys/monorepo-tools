<?php

declare(strict_types=1);

namespace Shopsys\Releaser\Tests\FileManipulator\UpgradeFileManipulator;

use PharIo\Version\Version;
use PHPUnit\Framework\TestCase;
use Shopsys\Releaser\FileManipulator\MonorepoUpgradeFileManipulator;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class MonorepoUpgradeFileManipulatorTest extends TestCase
{
    /**
     * @var \Shopsys\Releaser\FileManipulator\MonorepoUpgradeFileManipulator
     */
    private $upgradeFileManipulator;

    protected function setUp(): void
    {
        $this->upgradeFileManipulator = new MonorepoUpgradeFileManipulator('shopsys/shopsys');
    }

    public function test(): void
    {
        $changedContent = $this->upgradeFileManipulator->processFileToString(
            new SmartFileInfo(__DIR__ . '/Source/UPGRADE-monorepo-before.md'),
            new Version('v1.0.0')
        );

        $this->assertStringMatchesFormatFile(__DIR__ . '/Source/UPGRADE-monorepo-after.md', $changedContent);
    }

    public function testAlreadyDone(): void
    {
        $changedContent = $this->upgradeFileManipulator->processFileToString(
            new SmartFileInfo(__DIR__ . '/Source/UPGRADE-monorepo-after.md'),
            new Version('v1.0.0')
        );

        $this->assertStringMatchesFormatFile(__DIR__ . '/Source/UPGRADE-monorepo-after.md', $changedContent);
    }
}
