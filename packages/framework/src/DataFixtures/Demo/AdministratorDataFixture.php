<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Model\Administrator\AdministratorFacade;

class AdministratorDataFixture extends AbstractReferenceFixture
{
    const SUPERADMINISTRATOR = 'administrator_superadministrator';
    const ADMINISTRATOR = 'administrator_administrator';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\AdministratorFacade
     */
    private $administratorFacade;

    public function __construct(AdministratorFacade $administratorFacade)
    {
        $this->administratorFacade = $administratorFacade;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->createAdministratorReference(1, self::SUPERADMINISTRATOR);
        $this->createAdministratorReference(2, self::ADMINISTRATOR);
    }

    /**
     * Administrators are created (with specific ids) in database migration.
     *
     * @param int $administratorId
     * @param string $referenceName
     * @see \Shopsys\FrameworkBundle\Migrations\Version20180702111015
     */
    private function createAdministratorReference(int $administratorId, string $referenceName)
    {
        $administrator = $this->administratorFacade->getById($administratorId);
        $this->addReference($referenceName, $administrator);
    }
}
