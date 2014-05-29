<?php

namespace SS6\ShopBundle\Component;

use Composer\IO\IOInterface;
use Composer\Script\Event;
use SS6\Bootstrap;

require_once __DIR__ . '/../../../../app/Bootstrap.php';

class Enviroment {
	const FILE_DEVELOPMENT = 'DEVELOPMENT';
	const FILE_PRODUCTION = 'PRODUCTION';

	/**
	 * @param \Composer\Script\Event $event
	 */
	public static function checkEnviroment(Event $event) {
		$io = $event->getIO();
		/* @var $io \Composer\IO\IOInterface */
		if ($io->isInteractive() && self::getEnviromentSetting() === null) {
			if ($io->askConfirmation('Build in production enviroment? (Y/n): ', true)) {
				self::createFile(self::getRootDir() . '/' . self::FILE_PRODUCTION);
			} else {
				self::createFile(self::getRootDir() . '/' . self::FILE_DEVELOPMENT);
			}
		}
		self::printEnviromentInfo($io);
	}

	/**
	 * @return string
	 */
	public static function getEnviroment() {
		$enviromentSetting = self::getEnviromentSetting();
		return $enviromentSetting ?: Bootstrap::ENVIROMENT_PRODUCTION;
	}

	/**
	 * @param \Composer\IO\IOInterface $io
	 */
	public static function printEnviromentInfo(IOInterface $io) {
		$io->write("\nEnviroment is <info>" . self::getEnviroment() . "</info>\n");
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
	private static function getEnviromentSetting() {
		if (is_file(self::getRootDir() . '/' . self::FILE_DEVELOPMENT)) {
			return Bootstrap::ENVIROMENT_DEVELOPMENT;
		} elseif (is_file(self::getRootDir() . '/' . self::FILE_PRODUCTION)) {
			return Bootstrap::ENVIROMENT_PRODUCTION;
		}
		return null;
	}
}
