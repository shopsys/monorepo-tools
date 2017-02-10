<?php

namespace Shopsys\ShopBundle\Model\Pricing\Vat;

use Shopsys\ShopBundle\Model\Pricing\Vat\Vat;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatData;

class VatService
{

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Vat\VatData $vatData
     * @return \Shopsys\ShopBundle\Model\Pricing\Vat\Vat
     */
    public function create(VatData $vatData) {
        return new Vat($vatData);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Vat\Vat $vat
     * @param \Shopsys\ShopBundle\Model\Pricing\Vat\VatData $vatData
     * @return \Shopsys\ShopBundle\Model\Pricing\Vat\Vat
     */
    public function edit(Vat $vat, VatData $vatData) {
        $vat->edit($vatData);

        return $vat;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Vat\Vat $defaultVat
     * @param \Shopsys\ShopBundle\Model\Pricing\Vat\Vat $vatToDelete
     * @param \Shopsys\ShopBundle\Model\Pricing\Vat\Vat $newVat
     * @return \Shopsys\ShopBundle\Model\Pricing\Vat\Vat
     */
    public function getNewDefaultVat(Vat $defaultVat, Vat $vatToDelete, Vat $newVat) {
        if ($defaultVat->getId() === $vatToDelete->getId()) {
            return $newVat;
        }
        return $defaultVat;
    }

}
