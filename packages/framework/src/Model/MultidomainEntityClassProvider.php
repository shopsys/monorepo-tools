<?php

namespace Shopsys\FrameworkBundle\Model;

use Shopsys\FrameworkBundle\Component\Domain\Multidomain\MultidomainEntityClassProviderInterface;
use Shopsys\FrameworkBundle\Component\Setting\SettingValue;
use Shopsys\FrameworkBundle\Model\Category\CategoryDomain;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplate;
use Shopsys\FrameworkBundle\Model\Payment\PaymentDomain;
use Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain;
use Shopsys\FrameworkBundle\Model\Product\ProductDomain;
use Shopsys\FrameworkBundle\Model\Product\ProductVisibility;
use Shopsys\FrameworkBundle\Model\Transport\TransportDomain;

class MultidomainEntityClassProvider implements MultidomainEntityClassProviderInterface
{
    /**
     * @return string[]
     */
    public function getIgnoredMultidomainEntitiesNames(): array
    {
        return [
            SettingValue::class,
            ProductVisibility::class,
        ];
    }

    /**
     * @return string[]
     */
    public function getManualMultidomainEntitiesNames(): array
    {
        return [
            BrandDomain::class,
            CategoryDomain::class,
            MailTemplate::class,
            PaymentDomain::class,
            ProductDomain::class,
            TransportDomain::class,
        ];
    }
}
