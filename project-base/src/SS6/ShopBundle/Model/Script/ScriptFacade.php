<?php

namespace SS6\ShopBundle\Model\Script;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Script\ScriptRepository;

class ScriptFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Script\ScriptRepository
	 */
	private $scriptRepository;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Script\ScriptRepository $scriptRepository
	 */
	public function __construct(
		EntityManager $em,
		ScriptRepository $scriptRepository
	) {
		$this->em = $em;
		$this->scriptRepository = $scriptRepository;
	}

	/**
	 * @param string $placement
	 * @return \SS6\ShopBundle\Model\Script\Script[]
	 */
	public function getScriptsByPlacement($placement) {
		return $this->scriptRepository->getScriptsByPlacement($placement);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Script\ScriptData $scriptData
	 * @return \SS6\ShopBundle\Model\Script\Script
	 */
	public function create(ScriptData $scriptData) {
		$script = new Script($scriptData);

		$this->em->persist($script);
		$this->em->flush();

		return $script;
	}

}
