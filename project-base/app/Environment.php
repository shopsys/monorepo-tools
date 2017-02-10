<?php

namespace Shopsys;

use Composer\IO\IOInterface;
use Composer\Script\Event;

class Environment {
	const ENVIRONMENT_PRODUCTION = 'prod';
	const ENVIRONMENT_DEVELOPMENT = 'dev';
	const ENVIRONMENT_TEST = 'test';

	const FILE_DEVELOPMENT = 'DEVELOPMENT';
	const FILE_PRODUCTION = 'PRODUCTION';
	const FILE_TEST = 'TEST';

	/**
	 * @param \Composer\Script\Event $event
	 */
	public static function checkEnvironment(Event $event) {
		$io = $event->getIO();
		/* @var $io \Composer\IO\IOInterface */
		if ($io->isInteractive() && self::getEnvironmentSetting(false) === null) {
			if ($io->askConfirmation('Build in production environment? (Y/n): ', true)) {
				self::createFile(self::getRootDir() . '/' . self::FILE_PRODUCTION);
			} else {
				self::createFile(self::getRootDir() . '/' . self::FILE_DEVELOPMENT);
			}
		}
		self::printEnvironmentInfo($io);
	}

	/**
	 * @param bool $console
	 * @return string
	 */
	public static function getEnvironment($console) {
		$environmentSetting = self::getEnvironmentSetting($console);
		return $environmentSetting ?: self::ENVIRONMENT_PRODUCTION;
	}

	/**
	 * @param string $environment
	 */
	public static function isEnvironmentDebug($environment) {
		return $environment === self::ENVIRONMENT_DEVELOPMENT;
	}

	/**
	 * @param \Composer\IO\IOInterface $io
	 */
	public static function printEnvironmentInfo(IOInterface $io) {
		$io->write("\nEnvironment is <info>" . self::getEnvironment(false) . "</info>\n");
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
		return __DIR__ . '/..';
	}

	/**
	 * @param bool $ignoreTestFile
	 * @return string|null
	 */
	private static function getEnvironmentSetting($ignoreTestFile) {
		if (!$ignoreTestFile && is_file(self::getRootDir() . '/' . self::FILE_TEST)) {
			return self::ENVIRONMENT_TEST;
		} elseif (is_file(self::getRootDir() . '/' . self::FILE_DEVELOPMENT)) {
			return self::ENVIRONMENT_DEVELOPMENT;
		} elseif (is_file(self::getRootDir() . '/' . self::FILE_PRODUCTION)) {
			return self::ENVIRONMENT_PRODUCTION;
		}
		return null;
	}
}
