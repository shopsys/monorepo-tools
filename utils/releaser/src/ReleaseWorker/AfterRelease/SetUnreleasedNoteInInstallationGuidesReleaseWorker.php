<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\AfterRelease;

use Nette\Utils\FileSystem;
use PharIo\Version\Version;
use Shopsys\Releaser\FileManipulator\InstallationGuideFileManipulator;
use Shopsys\Releaser\FilesProvider\InstallationGuideFilesProvider;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;
use Symplify\MonorepoBuilder\Release\Message;

final class SetUnreleasedNoteInInstallationGuidesReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @var \Shopsys\Releaser\FileManipulator\InstallationGuideFileManipulator
     */
    protected $installationGuideFileManipulator;

    /**
     * @var \Shopsys\Releaser\FilesProvider\InstallationGuideFilesProvider
     */
    private $installationGuideFilesProvider;

    /**
     * @param \Shopsys\Releaser\FileManipulator\InstallationGuideFileManipulator $installationGuideFileManipulator
     * @param \Shopsys\Releaser\FilesProvider\InstallationGuideFilesProvider $installationGuideFilesProvider
     */
    public function __construct(
        InstallationGuideFileManipulator $installationGuideFileManipulator,
        InstallationGuideFilesProvider $installationGuideFilesProvider
    ) {
        $this->installationGuideFileManipulator = $installationGuideFileManipulator;
        $this->installationGuideFilesProvider = $installationGuideFilesProvider;
    }

    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return 'Set unreleased notes in installation guides for Docker on Linux, Mac, and Windows';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 150;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        $this->symfonyStyle->note('Setting note about the unreleased version in installation guides.');
        $fileInfos = $this->installationGuideFilesProvider->provide();
        foreach ($fileInfos as $fileInfo) {
            $newContent = $this->installationGuideFileManipulator->setUnreleasedNote($fileInfo, $version);
            FileSystem::write($fileInfo->getPathname(), $newContent);
        }
        $this->commit('docker installation guides: updated note about the unreleased version');
        $this->symfonyStyle->success(Message::SUCCESS);
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::AFTER_RELEASE;
    }
}
