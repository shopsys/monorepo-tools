<?php

namespace Shopsys\ShopBundle\Model\Administrator;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Administrator\Administrator as BaseAdministrator;
use Shopsys\FrameworkBundle\Model\Administrator\AdministratorData as BaseAdministratorData;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="administrators",
 *   indexes={
 *     @ORM\Index(columns={"username"})
 *   }
 * )
 */
class Administrator extends BaseAdministrator
{
    /**
     * @param \Shopsys\ShopBundle\Model\Administrator\AdministratorData $administratorData
     */
    public function __construct(BaseAdministratorData $administratorData)
    {
        parent::__construct($administratorData);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorData $administratorData
     * @param \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $encoderFactory
     * @param \Shopsys\FrameworkBundle\Model\Administrator\Administrator|null $administratorByUserName
     */
    public function edit(
        BaseAdministratorData $administratorData,
        EncoderFactoryInterface $encoderFactory,
        ?BaseAdministrator $administratorByUserName
    ) {
        parent::edit($administratorData, $encoderFactory, $administratorByUserName);
    }
}
