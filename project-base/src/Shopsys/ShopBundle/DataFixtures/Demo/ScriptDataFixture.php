<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Script\Script;
use SS6\ShopBundle\Model\Script\ScriptData;
use SS6\ShopBundle\Model\Script\ScriptFacade;

class ScriptDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$scriptData = new ScriptData();
		$scriptData->name = 'Demo skript 1';
		$scriptData->code = '<!-- demo script -->';
		$scriptData->placement = Script::PLACEMENT_ALL_PAGES;

		$this->createScript($scriptData);

		$scriptData->name = 'Demo skript 2';
		$scriptData->code = '<!-- script to display on order sent page -->';
		$scriptData->placement = Script::PLACEMENT_ORDER_SENT_PAGE;

		$this->createScript($scriptData);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Script\ScriptData $scriptData
	 */
	private function createScript(ScriptData $scriptData) {
		$scriptFacade = $this->get(ScriptFacade::class);
		/* @var $scriptFacade \SS6\ShopBundle\Model\Script\ScriptFacade */
		$scriptFacade->create($scriptData);
	}
}
