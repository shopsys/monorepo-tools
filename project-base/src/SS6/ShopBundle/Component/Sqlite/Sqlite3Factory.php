<?php

namespace SS6\ShopBundle\Component\Sqlite;

use SS6\ShopBundle\Component\Sqlite\Sqlite3ComplyingUmask;

class Sqlite3Factory {

	public function create($filename) {
		$sqlite = new Sqlite3ComplyingUmask($filename, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
		$sqlite->busyTimeout(60000);

		return $sqlite;
	}

}
