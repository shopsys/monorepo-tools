<?php

declare(strict_types=1);

namespace Shopsys\Releaser\Tests\FileManipulator\DockerComposerFileManipulator;

use PharIo\Version\Version;
use PHPUnit\Framework\TestCase;
use Shopsys\Releaser\FileManipulator\DockerComposerFileManipulator;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class DockerComposerFileManipulatorTest extends TestCase
{
    public function test(): void
    {
        $dockerComposerFileManipulator = new DockerComposerFileManipulator();

        $changedContent = $dockerComposerFileManipulator->processFileToString(
            new SmartFileInfo(__DIR__ . '/Source/docker-compose-before.yml.dist'),
            new Version('v1.0.0')
        );

        $this->assertStringMatchesFormatFile(__DIR__ . '/Source/docker-compose-after.yml.dist', $changedContent);
    }
}
