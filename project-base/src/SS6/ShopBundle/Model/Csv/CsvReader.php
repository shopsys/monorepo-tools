<?php

namespace SS6\ShopBundle\Model\Csv;

class CsvReader {

	/**
	 * @param string $filename
	 * @return array
	 */
	public function getRowsFromCsv($filename) {
		if (!file_exists($filename) || !is_readable($filename)) {
			throw new \Symfony\Component\Filesystem\Exception\FileNotFoundException();
		}

		$data = array();
		$handle = fopen($filename, 'r');
		if ($handle !== false) {
			$row = fgetcsv($handle, 0, ';');
			while ($row !== false) {
				$data[] = $row;
				$row = fgetcsv($handle, 0, ';');
			}
			fclose($handle);
		}
		return $data;
	}
}