<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker;

use Nette\Utils\FileSystem;
use PharIo\Version\Version;
use Shopsys\Releaser\FileManipulator\DockerComposeFileManipulator;
use Shopsys\Releaser\Finder\DockerComposeFilesProvider;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

final class UpdateDockerComposeToLatestReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var \Shopsys\Releaser\FileManipulator\DockerComposeFileManipulator
     */
    private $dockerComposerFileManipulator;

    /**
     * @var \Shopsys\Releaser\Finder\DockerComposeFilesProvider
     */
    private $dockerComposeFilesProvider;

    /**
     * @param \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle
     * @param \Shopsys\Releaser\FileManipulator\DockerComposeFileManipulator $dockerComposerFileManipulator
     * @param \Shopsys\Releaser\ReleaseWorker\Shopsys\Releaser\Finder\DockerComposeFilesProvider $dockerComposeFilesProvider
     */
    public function __construct(
        SymfonyStyle $symfonyStyle,
        DockerComposeFileManipulator $dockerComposerFileManipulator,
        DockerComposeFilesProvider $dockerComposeFilesProvider
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->dockerComposerFileManipulator = $dockerComposerFileManipulator;
        $this->dockerComposeFilesProvider = $dockerComposeFilesProvider;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Update micro-service references in all docker-composer.yml.dist from released version to "latest"';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 660;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        return;

        foreach ($this->dockerComposeFilesProvider->provide() as $fileInfo) {
            $newContent = $this->dockerComposerFileManipulator->processFileToString($fileInfo, $version->getVersionString(), 'latest');

            // save
            FileSystem::write($fileInfo->getPathname(), $newContent);
        }

        $this->symfonyStyle->success('Ok');
    }
}
