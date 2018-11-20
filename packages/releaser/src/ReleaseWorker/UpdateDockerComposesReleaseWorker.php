<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker;

use Nette\Utils\FileSystem;
use PharIo\Version\Version;
use Shopsys\Releaser\FileManipulator\DockerComposerFileManipulator;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

final class UpdateDockerComposesReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var \Shopsys\Releaser\FileManipulator\DockerComposerFileManipulator
     */
    private $dockerComposerFileManipulator;

    /**
     * @param \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle
     * @param \Shopsys\Releaser\FileManipulator\DockerComposerFileManipulator $dockerComposerFileManipulator
     */
    public function __construct(
        SymfonyStyle $symfonyStyle,
        DockerComposerFileManipulator $dockerComposerFileManipulator
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->dockerComposerFileManipulator = $dockerComposerFileManipulator;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Update micro-service references in all docker-composer.yml.dist from "latest" to released version';
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
        return;

        $finder = Finder::create()
            ->in(getcwd())
            ->exclude('vendor')
            ->files()
            ->name('#\.yml\.dist$#');

        /** @var \Symfony\Component\Finder\SplFileInfo $fileInfo */
        foreach ($finder as $fileInfo) {
            $newContent = $this->dockerComposerFileManipulator->processFileToString($fileInfo, $version);

            // save
            FileSystem::write($fileInfo->getPathname(), $newContent);
        }

        $this->symfonyStyle->success('Ok');
    }
}
