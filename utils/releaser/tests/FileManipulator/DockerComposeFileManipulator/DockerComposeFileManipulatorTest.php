<?php

declare(strict_types=1);

namespace Shopsys\Releaser\Tests\FileManipulator\DockerComposeFileManipulator;

use PharIo\Version\Version;
use PHPUnit\Framework\TestCase;
use Shopsys\Releaser\FileManipulator\DockerComposeFileManipulator;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class DockerComposeFileManipulatorTest extends TestCase
{
    public function test(): void
    {
        $dockerComposeFileManipulator = new DockerComposeFileManipulator();

        $changedContent = $dockerComposeFileManipulator->processFileToString(
            new SmartFileInfo(__DIR__ . '/Source/docker-compose-before.yml.dist'),
            new Version('v1.0.0')
        );

        $this->assertStringMatchesFormatFile(__DIR__ . '/Source/docker-compose-after.yml.dist', $changedContent);
    }
}
