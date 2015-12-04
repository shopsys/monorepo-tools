<?php

namespace SS6\ShopBundle\Component\Sqlite;

use SQLite3;

/**
 * sqlite3 library ignores umask settings.
 * This class sets file permissions according to umask.
 */
class Sqlite3ComplyingUmask extends SQLite3 {

	/**
	 * {@inheritDoc}
	 */
	public function __construct($filename, $flags = null, $encryptionKey = null) {
		$fileExisted = file_exists($filename);

		parent::__construct($filename, $flags, $encryptionKey);

		$this->fixFilePermissions($filename, $fileExisted);
	}

	/**
	 * {@inheritDoc}
	 */
	public function open($filename, $flags = null, $encryptionKey = null) {
		$fileExisted = file_exists($filename);

		parent::open($filename, $flags, $encryptionKey);

		$this->fixFilePermissions($filename, $fileExisted);
	}

	/**
	 * @param string $filename
	 * @param bool $fileExisted
	 */
	private function fixFilePermissions($filename, $fileExisted) {
		if (!$fileExisted && file_exists($filename)) {
			chmod($filename, 0666 & ~umask());
		}
	}

}
