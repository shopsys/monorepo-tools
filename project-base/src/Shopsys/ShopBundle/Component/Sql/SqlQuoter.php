<?php

namespace Shopsys\ShopBundle\Component\Sql;

use Doctrine\ORM\EntityManager;

class SqlQuoter {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @param string[] $identifiers
	 * @return string[]
	 */
	public function quoteIdentifiers(array $identifiers) {
		return array_map(
			function ($identifier) {
				return $this->quoteIdentifier($identifier);
			},
			$identifiers
		);
	}

	/**
	 * @param string $identifier
	 * @return string
	 */
	public function quoteIdentifier($identifier) {
		return $this->em->getConnection()->quoteIdentifier($identifier);
	}

	/**
	 * @param mixed $input
	 * @param string|null $type
	 * @return string
	 */
	public function quote($input, $type = null) {
		return $this->em->getConnection()->quote($input, $type);
	}

}
