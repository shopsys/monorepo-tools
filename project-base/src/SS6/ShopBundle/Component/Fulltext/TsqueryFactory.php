<?php

namespace SS6\ShopBundle\Component\Fulltext;

class TsqueryFactory {

	/**
	 * @param string|null $searchText
	 * @return string
	 */
	public function getTsqueryWithAndConditions($searchText) {
		$tokens = $this->splitToTokens($searchText);

		return implode(' & ', $tokens);
	}

	/**
	 * @param string|null $searchText
	 * @return string[]
	 */
	private function splitToTokens($searchText) {
		return preg_split(
			'/[^\w-]+/ui',
			$searchText,
			-1,
			PREG_SPLIT_NO_EMPTY
		);
	}

}
