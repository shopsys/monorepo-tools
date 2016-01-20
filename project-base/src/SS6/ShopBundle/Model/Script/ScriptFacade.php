<?php

namespace SS6\ShopBundle\Model\Script;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Script\ScriptRepository;

class ScriptFacade {

	const VARIABLE_NUMBER = '{number}';
	const VARIABLE_TOTAL_PRICE = '{total_price}';

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
	 * @return \SS6\ShopBundle\Model\Script\Script[]
	 */
	public function getAll() {
		return $this->scriptRepository->getAll();
	}

	/**
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getAllQueryBuilder() {
		return $this->scriptRepository->getAllQueryBuilder();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Script\Script
	 */
	public function getById($scriptId) {
		return $this->scriptRepository->getById($scriptId);
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

	/**
	 * @param int $scriptId
	 * @param \SS6\ShopBundle\Model\Script\ScriptData $scriptData
	 * @return \SS6\ShopBundle\Model\Script\Script
	 */
	public function edit($scriptId, ScriptData $scriptData) {
		$script = $this->scriptRepository->getById($scriptId);

		$script->edit($scriptData);

		$this->em->persist($script);
		$this->em->flush();

		return $script;
	}

	/**
	 * @param int $scriptId
	 */
	public function delete($scriptId) {
		$script = $this->scriptRepository->getById($scriptId);

		$this->em->remove($script);
		$this->em->flush();
	}

	/**
	 * @return string[]
	 */
	public function getAllPagesScriptCodes() {
		$allPagesScripts = $this->scriptRepository->getScriptsByPlacement(Script::PLACEMENT_ALL_PAGES);
		$scriptCodes = [];

		foreach ($allPagesScripts as $script) {
			$scriptCodes[] = $script->getCode();
		}

		return $scriptCodes;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return string[]
	 */
	public function getOrderSentPageScriptCodesWithReplacedVariables(Order $order) {
		$scripts = $this->scriptRepository->getScriptsByPlacement(Script::PLACEMENT_ORDER_SENT_PAGE);
		$scriptCodes = [];

		foreach ($scripts as $script) {
			$scriptCodes[] = $this->replaceVariables($script->getCode(), $order);
		}

		return $scriptCodes;
	}

	/**
	 * @param string $code
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return string
	 */
	private function replaceVariables($code, Order $order) {
		$variableReplacements = [
			self::VARIABLE_NUMBER => $order->getNumber(),
			self::VARIABLE_TOTAL_PRICE => $order->getTotalPriceWithVat(),
		];

		return strtr($code, $variableReplacements);
	}
}
