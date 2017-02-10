<?php

namespace Shopsys\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Model\Administrator\AdministratorData;
use Shopsys\ShopBundle\Model\Administrator\AdministratorFacade;

class AdministratorDataFixture extends AbstractReferenceFixture
{
    const SUPERADMINISTRATOR = 'administrator_superadministrator';
    const ADMINISTRATOR = 'administrator_administrator';

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {
        $superadminData = new AdministratorData(true);
        $superadminData->username = 'superadmin';
        $superadminData->realName = 'superadmin';
        $superadminData->email = 'no-reply@netdevelo.cz';
        $superadminData->password = 'admin123';
        $this->createAdministrator($superadminData, self::SUPERADMINISTRATOR);

        $administratorData = new AdministratorData();
        $administratorData->username = 'admin';
        $administratorData->realName = 'admin';
        $administratorData->password = 'admin123';
        $administratorData->email = 'no-reply@netdevelo.cz';
        $this->createAdministrator($administratorData, self::ADMINISTRATOR);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Administrator\AdministratorData $administratorData
     * @param string|null $referenceName
     */
    private function createAdministrator(AdministratorData $administratorData, $referenceName = null) {
        $administratorFacade = $this->get(AdministratorFacade::class);
        /* @var $administratorFacade \Shopsys\ShopBundle\Model\Administrator\AdministratorFacade */

        $administrator = $administratorFacade->create($administratorData);
        if ($referenceName !== null) {
            $this->addReference($referenceName, $administrator);
        }
    }
}
