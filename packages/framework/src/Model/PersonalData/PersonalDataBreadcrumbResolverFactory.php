<?php

namespace Shopsys\FrameworkBundle\Model\PersonalData;

use Shopsys\FrameworkBundle\Component\Breadcrumb\BreadcrumbGeneratorInterface;
use Shopsys\FrameworkBundle\Component\Breadcrumb\BreadcrumbItem;

class PersonalDataBreadcrumbResolverFactory implements BreadcrumbGeneratorInterface
{
    /**
     * @inheritdoc
     */
    public function getBreadcrumbItems($routeName, array $routeParameters = [])
    {
        return [
            new BreadcrumbItem(t('Personal information overview')),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRouteNames()
    {
        return [
            'front_personal_data',
            'front_personal_data_access',
        ];
    }
}
