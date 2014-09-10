<?php

namespace SS6\ShopBundle\Model\Csv;

class CsvReader {

	/**
	 * @param string $filename
	 * @return array
	 */
	public function getRowsFromCsv($filename, $delimiter = ';') {
		if (!file_exists($filename) || !is_readable($filename)) {
			throw new \Symfony\Component\Filesystem\Exception\FileNotFoundException();
		}

		$rows = array();
		$handle = fopen($filename, 'r');
		if ($handle !== false) {
			do {
				$row = fgetcsv($handle, 0, $delimiter);
				if ($row !== false) {
					$rows[] = $row;
				} else {
					break;
				}
			} while (true);

			fclose($handle);
		}
		return $rows;
	}
}