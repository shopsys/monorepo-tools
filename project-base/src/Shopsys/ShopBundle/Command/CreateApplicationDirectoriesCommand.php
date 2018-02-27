<?php

namespace Shopsys\ShopBundle\Command;

use Shopsys\ShopBundle\Component\Image\DirectoryStructureCreator as ImageDirectoryStructureCreator;
use Shopsys\ShopBundle\Component\UploadedFile\DirectoryStructureCreator as UploadedFileDirectoryStructureCreator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class CreateApplicationDirectoriesCommand extends Command
{

    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:create-directories';

    /**
     * @var string
     */
    private $rootDirectory;

    /**
     * @var string
     */
    private $webDirectory;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\DirectoryStructureCreator
     */
    private $imageDirectoryStructureCreator;

    /**
     * @var \Shopsys\ShopBundle\Component\UploadedFile\DirectoryStructureCreator
     */
    private $uploadedFileDirectoryStructureCreator;

    /**
     * @param string $rootDirectory
     * @param string $webDirectory
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     * @param \Shopsys\ShopBundle\Component\Image\DirectoryStructureCreator $imageDirectoryStructureCreator
     * @param \Shopsys\ShopBundle\Component\UploadedFile\DirectoryStructureCreator $uploadedFileDirectoryStructureCreator
     */
    public function __construct(
        $rootDirectory,
        $webDirectory,
        Filesystem $filesystem,
        ImageDirectoryStructureCreator $imageDirectoryStructureCreator,
        UploadedFileDirectoryStructureCreator $uploadedFileDirectoryStructureCreator
    ) {
        $this->rootDirectory = $rootDirectory;
        $this->webDirectory = $webDirectory;
        $this->filesystem = $filesystem;
        $this->imageDirectoryStructureCreator = $imageDirectoryStructureCreator;
        $this->uploadedFileDirectoryStructureCreator = $uploadedFileDirectoryStructureCreator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Create application directories for locks, docs, content, images, uploaded files, etc.');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->createMiscellaneousDirectories($output);
        $this->createImageDirectories($output);
        $this->createUploadedFileDirectories($output);
    }

    private function createMiscellaneousDirectories(OutputInterface $output)
    {
        $directories = [
            $this->rootDirectory . '/build/stats',
            $this->rootDirectory . '/docs/generated',
            $this->rootDirectory . '/var/cache',
            $this->rootDirectory . '/var/lock',
            $this->rootDirectory . '/var/logs',
            $this->rootDirectory . '/var/errorPages',
            $this->webDirectory . '/assets/admin/styles',
            $this->webDirectory . '/assets/frontend/styles',
            $this->webDirectory . '/assets/scripts',
            $this->webDirectory . '/content/feeds',
            $this->webDirectory . '/content/sitemaps',
            $this->webDirectory . '/content/wysiwyg',
        ];

        $this->filesystem->mkdir($directories);

        $output->writeln('<fg=green>Miscellaneous application directories were successfully created.</fg=green>');
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function createImageDirectories(OutputInterface $output)
    {
        $this->imageDirectoryStructureCreator->makeImageDirectories();

        $output->writeln('<fg=green>Directories for images were successfully created.</fg=green>');
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function createUploadedFileDirectories(OutputInterface $output)
    {
        $this->uploadedFileDirectoryStructureCreator->makeUploadedFileDirectories();

        $output->writeln('<fg=green>Directories for UploadedFile entities were successfully created.</fg=green>');
    }
}
