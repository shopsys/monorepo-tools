<?php

namespace Shopsys\ShopBundle\Command;

use Doctrine\ORM\Query\ResultSetMapping;
use Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZipArchive;

class ImageDemoCommand extends ContainerAwareCommand {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	protected function configure() {
		$this
			->setName('shopsys:image:demo')
			->setDescription('Download demo images');
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->filesystem = $this->getContainer()->get('filesystem');
		$this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

		$archiveUrl = $this->getContainer()->getParameter('ss6.demo_images_archive_url');
		$dqlUrl = $this->getContainer()->getParameter('ss6.demo_images_dql_url');
		$cachePath = $this->getContainer()->getParameter('kernel.cache_dir');
		$localArchiveFilepath = $cachePath . '/' . 'demoImages.zip';
		$imagesPath = $this->getContainer()->getParameter('ss6.image_dir');
		$domainImagesPath = $this->getContainer()->getParameter('ss6.domain_images_dir');
		$unpackedDomainImagesPath = $imagesPath . 'domain';

		if ($this->downloadImages($output, $archiveUrl, $localArchiveFilepath)) {
			if ($this->unpackImages($output, $imagesPath, $localArchiveFilepath)) {
				$this->moveFiles($unpackedDomainImagesPath, $domainImagesPath);
				$this->loadDbChanges($output, $dqlUrl);
			}
		}

		$this->cleanUp($output, [$localArchiveFilepath, $unpackedDomainImagesPath]);
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 * @param string $imagesPath
	 * @param string $localArchiveFilepath
	 * @return bool
	 */
	private function unpackImages(OutputInterface $output, $imagesPath, $localArchiveFilepath) {
		$zipArchive = new ZipArchive();

		$result = $zipArchive->open($localArchiveFilepath);
		if ($result !== true) {
			$output->writeln('<fg=red>Unpacking of images archive failed</fg=red>');
			return false;
		}

		$zipArchive->extractTo($imagesPath);
		$zipArchive->close();
		$output->writeln('<fg=green>Unpacking of images archive was successfully completed</fg=green>');

		return true;
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 * @param string $dqlUrl
	 */
	private function loadDbChanges(OutputInterface $output, $dqlUrl) {
		$fileContents = file_get_contents($dqlUrl);
		if ($fileContents === false) {
			$output->writeln('<fg=red>Download of DB changes failed</fg=red>');
			return;
		}
		$sqlQueries = explode(';', $fileContents);
		$sqlQueries = array_map('trim', $sqlQueries);
		$sqlQueries = array_filter($sqlQueries);

		$rsm = new ResultSetMapping();
		foreach ($sqlQueries as $sqlQuery) {
			$this->em->createNativeQuery($sqlQuery, $rsm)->execute();
		}
		$output->writeln('<fg=green>DB changes were successfully applied (queries: ' . count($sqlQueries) . ')</fg=green>');
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 * @param string $archiveUrl
	 * @param string $localArchiveFilepath
	 * @return bool
	 */
	private function downloadImages(OutputInterface $output, $archiveUrl, $localArchiveFilepath) {
		$output->writeln('Start downloading demo images');

		try {
			$this->filesystem->copy($archiveUrl, $localArchiveFilepath, true);
		} catch (Exception $e) {
			$output->writeln('<fg=red>Downloading of demo images failed</fg=red>');
			$output->writeln('<fg=red>Exception: ' . $e->getMessage() . '</fg=red>');

			return false;
		}

		$output->writeln('Success downloaded');
		return true;
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 * @param string[] $pathsToRemove
	 */
	private function cleanUp(OutputInterface $output, $pathsToRemove) {
		try {
			$this->filesystem->remove($pathsToRemove);
		} catch (Exception $e) {
			$output->writeln('<fg=red>Deleting of demo archive in cache failed</fg=red>');
			$output->writeln('<fg=red>Exception: ' . $e->getMessage() . '</fg=red>');
		}
	}

	/**
	 * @param string $origin
	 * @param string $target
	 */
	private function moveFiles($origin, $target) {
		$files = scandir($origin);
		foreach ($files as $file) {
			$filepath = $origin . '/' . $file;
			if (is_file($filepath)) {
				$newFilepath = $target . '/' . $file;
				$this->filesystem->rename($filepath, $newFilepath, true);
			}
		}
	}

}
