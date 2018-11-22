<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker;

use Nette\Utils\FileSystem;
use PharIo\Version\Version;
use Shopsys\Releaser\FileManipulator\DockerComposeFileManipulator;
use Shopsys\Releaser\FilesProvider\DockerComposeFilesProvider;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Message;

final class UpdateDockerComposeToLatestReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var \Shopsys\Releaser\FileManipulator\DockerComposeFileManipulator
     */
    private $dockerComposeFileManipulator;

    /**
     * @var \Shopsys\Releaser\FilesProvider\DockerComposeFilesProvider
     */
    private $dockerComposeFilesProvider;

    /**
     * @param \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle
     * @param \Shopsys\Releaser\FileManipulator\DockerComposeFileManipulator $dockerComposeFileManipulator
     * @param \Shopsys\Releaser\FilesProvider\DockerComposeFilesProvider $dockerComposeFilesProvider
     */
    public function __construct(
        SymfonyStyle $symfonyStyle,
        DockerComposeFileManipulator $dockerComposeFileManipulator,
        DockerComposeFilesProvider $dockerComposeFilesProvider
    ) {
        $this->symfonyStyle = $symfonyStyle;
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
        return 640;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        return;

        foreach ($this->dockerComposeFilesProvider->provide() as $fileInfo) {
            $newContent = $this->dockerComposeFileManipulator->processFileToString($fileInfo, $version->getVersionString(), 'latest');

            // save
            FileSystem::write($fileInfo->getPathname(), $newContent);
        }

        $this->symfonyStyle->success(Message::SUCCESS);
    }
}
