<?php

namespace SS6\ShopBundle\Component;

use Composer\IO\IOInterface;
use Composer\Script\Event;
use SS6\Bootstrap;

require_once __DIR__ . '/../../../../app/Bootstrap.php';

class Environment {
	const FILE_DEVELOPMENT = 'DEVELOPMENT';
	const FILE_PRODUCTION = 'PRODUCTION';

	/**
	 * @param \Composer\Script\Event $event
	 */
	public static function checkEnvironment(Event $event) {
		$io = $event->getIO();
		/* @var $io \Composer\IO\IOInterface */
		if ($io->isInteractive() && self::getEnvironmentSetting() === null) {
			if ($io->askConfirmation('Build in production environment? (Y/n): ', true)) {
				self::createFile(self::getRootDir() . '/' . self::FILE_PRODUCTION);
			} else {
				self::createFile(self::getRootDir() . '/' . self::FILE_DEVELOPMENT);
			}
		}
		self::printEnvironmentInfo($io);
	}

	/**
	 * @return string
	 */
	public static function getEnvironment() {
		$environmentSetting = self::getEnvironmentSetting();
		return $environmentSetting ?: Bootstrap::ENVIRONMENT_PRODUCTION;
	}

	/**
	 * @param \Composer\IO\IOInterface $io
	 */
	public static function printEnvironmentInfo(IOInterface $io) {
		$io->write("\nEnvironment is <info>" . self::getEnvironment() . "</info>\n");
	}

	/**
	 * @param string $filepath
	 */
	private static function createFile($filepath) {
		$file = fopen($filepath, 'w');
		fclose($file);
	}

	/**
	 * @return string
	 */
	private static function getRootDir() {
		return __DIR__ . '/../../../..';
	}

	/**
	 * @return string|null
	 */
	private static function getEnvironmentSetting() {
		if (is_file(self::getRootDir() . '/' . self::FILE_DEVELOPMENT)) {
			return Bootstrap::ENVIRONMENT_DEVELOPMENT;
		} elseif (is_file(self::getRootDir() . '/' . self::FILE_PRODUCTION)) {
			return Bootstrap::ENVIRONMENT_PRODUCTION;
		}
		return null;
	}
}
