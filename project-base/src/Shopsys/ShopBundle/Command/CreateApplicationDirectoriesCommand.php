<?php

namespace Shopsys\ShopBundle\Command;

use Shopsys\ShopBundle\Component\Image\DirectoryStructureCreator as ImageDirectoryStructureCreator;
use Shopsys\ShopBundle\Component\UploadedFile\DirectoryStructureCreator as UploadedFileDirectoryStructureCreator;
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
        $directories = [];
        $directories[] = $this->getContainer()->getParameter('kernel.root_dir') . '/lock';
        $directories[] = $this->getContainer()->getParameter('kernel.root_dir') . '/errorPages';
        $directories[] = $this->getContainer()->getParameter('shopsys.root_dir') . '/docs/admin';
        $directories[] = $this->getContainer()->getParameter('shopsys.root_dir') . '/docs/frontend';
        $directories[] = $this->getContainer()->getParameter('shopsys.web_dir') . '/assets/admin/styles';
        $directories[] = $this->getContainer()->getParameter('shopsys.web_dir') . '/assets/frontend/styles';
        $directories[] = $this->getContainer()->getParameter('shopsys.web_dir') . '/assets/scripts';
        $directories[] = $this->getContainer()->getParameter('shopsys.web_dir') . '/content/feeds';
        $directories[] = $this->getContainer()->getParameter('shopsys.web_dir') . '/content/sitemaps';
        $directories[] = $this->getContainer()->getParameter('shopsys.web_dir') . '/content/wysiwyg';

        $filesystem = $this->getContainer()->get(Filesystem::class);
        /* @var $filesystem \Symfony\Component\Filesystem\Filesystem */

        $filesystem->mkdir($directories);

        $output->writeln('<fg=green>Miscellaneous application directories were successfully created.</fg=green>');
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function createImageDirectories(OutputInterface $output)
    {
        $imageDirectoryStructureCreator = $this->getContainer()->get(ImageDirectoryStructureCreator::class);
        /* @var $imageDirectoryStructureCreator \Shopsys\ShopBundle\Component\Image\DirectoryStructureCreator */
        $imageDirectoryStructureCreator->makeImageDirectories();

        $output->writeln('<fg=green>Directories for images were successfully created.</fg=green>');
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function createUploadedFileDirectories(OutputInterface $output)
    {
        $uploadedFileDirectoryStructureCreator = $this->getContainer()->get(UploadedFileDirectoryStructureCreator::class);
        /* @var $uploadedFileDirectoryStructureCreator \Shopsys\ShopBundle\Component\UploadedFile\DirectoryStructureCreator */
        $uploadedFileDirectoryStructureCreator->makeUploadedFileDirectories();

        $output->writeln('<fg=green>Directories for UploadedFile entities were successfully created.</fg=green>');
    }
}
