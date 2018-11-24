<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\ReleaseCandidate;

use Nette\Utils\FileSystem;
use PharIo\Version\Version;
use Shopsys\Releaser\FileManipulator\DockerComposeFileManipulator;
use Shopsys\Releaser\FilesProvider\DockerComposeFilesProvider;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Release\Message;

final class UpdateDockerComposeToVersionReleaseWorker extends AbstractShopsysReleaseWorker
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
            'Update micro-service references in all docker-composer.yml.dist from "latest" to "%s" version',
            $version->getVersionString()
        );
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 780;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        foreach ($this->dockerComposeFilesProvider->provide() as $fileInfo) {
            $newContent = $this->dockerComposeFileManipulator->processFileToString($fileInfo, 'latest', $version->getVersionString());

            // save
            FileSystem::write($fileInfo->getPathname(), $newContent);
        }

        // @todo 'git commit -m "all shopsys Docker images are now used in X.X version instead of the latest" && git push

        $this->symfonyStyle->success(Message::SUCCESS);
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE_CANDIDATE;
    }
}
