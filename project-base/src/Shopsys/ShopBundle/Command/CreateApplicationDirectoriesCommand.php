<?php

namespace Shopsys\ShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class CreateApplicationDirectoriesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('shopsys:create-directories')
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
        $rootDirectory = $this->getContainer()->getParameter('shopsys.root_dir');
        $webDirectory = $this->getContainer()->getParameter('shopsys.web_dir');

        $directories = [
            $rootDirectory . '/build/stats',
            $rootDirectory . '/docs/generated',
            $rootDirectory . '/var/cache',
            $rootDirectory . '/var/lock',
            $rootDirectory . '/var/logs',
            $rootDirectory . '/var/errorPages',
            $webDirectory . '/assets/admin/styles',
            $webDirectory . '/assets/frontend/styles',
            $webDirectory . '/assets/scripts',
            $webDirectory . '/content/feeds',
            $webDirectory . '/content/sitemaps',
            $webDirectory . '/content/wysiwyg',
        ];

        $filesystem = $this->getContainer()->get('filesystem');
        /* @var $filesystem \Symfony\Component\Filesystem\Filesystem */

        $filesystem->mkdir($directories);

        $output->writeln('<fg=green>Miscellaneous application directories were successfully created.</fg=green>');
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function createImageDirectories(OutputInterface $output)
    {
        $imageDirectoryStructureCreator = $this->getContainer()
            ->get('shopsys.shop.component.image.directory_structure_creator');
        /* @var $imageDirectoryStructureCreator \Shopsys\ShopBundle\Component\Image\DirectoryStructureCreator */
        $imageDirectoryStructureCreator->makeImageDirectories();

        $output->writeln('<fg=green>Directories for images were successfully created.</fg=green>');
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function createUploadedFileDirectories(OutputInterface $output)
    {
        $uploadedFileDirectoryStructureCreator = $this->getContainer()
            ->get('shopsys.shop.component.uploaded_file.directory_structure_creator');
        /* @var $uploadedFileDirectoryStructureCreator \Shopsys\ShopBundle\Component\UploadedFile\DirectoryStructureCreator */
        $uploadedFileDirectoryStructureCreator->makeUploadedFileDirectories();

        $output->writeln('<fg=green>Directories for UploadedFile entities were successfully created.</fg=green>');
    }
}
