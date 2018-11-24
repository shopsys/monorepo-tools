<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\AfterRelease;

use Nette\Utils\FileSystem;
use PharIo\Version\Version;
use Shopsys\Releaser\FileManipulator\DockerComposeFileManipulator;
use Shopsys\Releaser\FilesProvider\DockerComposeFilesProvider;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;

final class UpdateDockerComposeToLatestReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @var \Shopsys\Releaser\FileManipulator\DockerComposeFileManipulator
     */
    private $dockerComposeFileManipulator;

    /**
     * @var \Shopsys\Releaser\FilesProvider\DockerComposeFilesProvider
     */
    private $dockerComposeFilesProvider;

    /**
     * @param \Shopsys\Releaser\FileManipulator\DockerComposeFileManipulator $dockerComposeFileManipulator
     * @param \Shopsys\Releaser\FilesProvider\DockerComposeFilesProvider $dockerComposeFilesProvider
     */
    public function __construct(
        DockerComposeFileManipulator $dockerComposeFileManipulator,
        DockerComposeFilesProvider $dockerComposeFilesProvider
    ) {
        $this->dockerComposeFileManipulator = $dockerComposeFileManipulator;
        $this->dockerComposeFilesProvider = $dockerComposeFilesProvider;
    }

    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return sprintf(
            'Update micro-service references in all docker-composer.yml.dist from "%s" version to "latest"',
            $version->getVersionString()
        );
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 180;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        foreach ($this->dockerComposeFilesProvider->provide() as $fileInfo) {
            $newContent = $this->dockerComposeFileManipulator->processFileToString($fileInfo, $version->getVersionString(), 'latest');

            // save
            FileSystem::write($fileInfo->getPathname(), $newContent);
        }

        // @todo 'git commit -m "all shopsys Docker images are now used in latest version" && git push
        $this->symfonyStyle->confirm('Confirm the docker images were committed');
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::AFTER_RELEASE;
    }
}
