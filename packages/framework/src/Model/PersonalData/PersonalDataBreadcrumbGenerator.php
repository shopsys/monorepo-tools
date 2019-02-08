<?php

namespace Shopsys\FrameworkBundle\Model\PersonalData;

use Shopsys\FrameworkBundle\Component\Breadcrumb\BreadcrumbGeneratorInterface;
use Shopsys\FrameworkBundle\Component\Breadcrumb\BreadcrumbItem;

class PersonalDataBreadcrumbGenerator implements BreadcrumbGeneratorInterface
{
    /**
     * @inheritdoc
     */
    public function getBreadcrumbItems($routeName, array $routeParameters = [])
    {
        if (in_array($routeName, $this->getPersonalDataRouteNames(), true)) {
            $breadcrumbItem = new BreadcrumbItem(t('Personal information overview'));
        } else {
            $breadcrumbItem = new BreadcrumbItem(t('Personal information export'));
        }

        return [$breadcrumbItem];
    }

    /**
     * @inheritdoc
     */
    public function getRouteNames()
    {
        return [
            'front_personal_data',
            'front_personal_data_access',
            'front_personal_data_export',
            'front_personal_data_access_export',
        ];
    }

    /**
     * @return array
     */
    protected function getPersonalDataRouteNames()
    {
        return [
            'front_personal_data',
            'front_personal_data_access',
        ];
    }
}
