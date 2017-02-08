<?php

namespace Shopsys\ShopBundle\Component\Fulltext;

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
	 * @return string
	 */
	public function getTsqueryWithAndConditionsAndPrefixMatchForLastWord($searchText) {
		$tokens = $this->splitToTokensWithPrefixMatchForLastToken($searchText);

		return implode(' & ', $tokens);
	}

	/**
	 * @param string|null $searchText
	 * @return string
	 */
	public function getTsqueryWithOrConditions($searchText) {
		$tokens = $this->splitToTokens($searchText);

		return implode(' | ', $tokens);
	}

	/**
	 * @param string|null $searchText
	 * @return string
	 */
	public function getTsqueryWithOrConditionsAndPrefixMatchForLastWord($searchText) {
		$tokens = $this->splitToTokensWithPrefixMatchForLastToken($searchText);

		return implode(' | ', $tokens);
	}

	private function splitToTokensWithPrefixMatchForLastToken($searchText) {
		$tokens = $this->splitToTokens($searchText);

		if (count($tokens)) {
			end($tokens);
			$lastKey = key($tokens);
			$tokens[$lastKey] = $tokens[$lastKey] . ':*';
		}

		return $tokens;
	}

	/**
	 * @param string|null $searchText
	 * @return bool
	 */
	public function isValidSearchText($searchText) {
		return count($this->splitToTokens($searchText)) > 0;
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
