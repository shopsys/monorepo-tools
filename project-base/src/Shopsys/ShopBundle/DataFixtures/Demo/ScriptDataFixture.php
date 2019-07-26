<?php

declare(strict_types=1);

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Model\Script\Script;
use Shopsys\FrameworkBundle\Model\Script\ScriptDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Script\ScriptFacade;

class ScriptDataFixture extends AbstractReferenceFixture
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Script\ScriptFacade
     */
    protected $scriptFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Script\ScriptDataFactoryInterface
     */
    protected $scriptDataFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Script\ScriptFacade $scriptFacade
     * @param \Shopsys\FrameworkBundle\Model\Script\ScriptDataFactoryInterface $scriptDataFactory
     */
    public function __construct(
        ScriptFacade $scriptFacade,
        ScriptDataFactoryInterface $scriptDataFactory
    ) {
        $this->scriptFacade = $scriptFacade;
        $this->scriptDataFactory = $scriptDataFactory;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $scriptData = $this->scriptDataFactory->create();
        $scriptData->name = 'Demo skript 1';
        $scriptData->code = '<!-- demo script -->';
        $scriptData->placement = Script::PLACEMENT_ALL_PAGES;

        $this->scriptFacade->create($scriptData);

        $scriptData->name = 'Demo skript 2';
        $scriptData->code = '<!-- script to display on order sent page -->';
        $scriptData->placement = Script::PLACEMENT_ORDER_SENT_PAGE;

        $this->scriptFacade->create($scriptData);
    }
}
