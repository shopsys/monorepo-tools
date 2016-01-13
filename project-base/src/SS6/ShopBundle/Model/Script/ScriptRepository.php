<?php

namespace SS6\ShopBundle\Model\Script;

use Doctrine\ORM\EntityManager;

class ScriptRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getScriptRepository() {
		return $this->em->getRepository(Script::class);
	}

	/**
	 * @param string $placement
	 * @return \SS6\ShopBundle\Model\Script\Script[]
	 */
	public function getScriptsByPlacement($placement) {
		return $this->getScriptRepository()->findBy(['placement' => $placement]);
	}

}
