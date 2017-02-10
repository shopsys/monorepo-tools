<?php

namespace Shopsys\ShopBundle\Model\Security;

interface UniqueLoginInterface {

    /**
     * @return string
     */
    public function getLoginToken();

    /**
     * @param string $loginToken
     */
    public function setLoginToken($loginToken);

    /**
     * @return bool
     */
    public function isMultidomainLogin();

    /**
     * @param bool $multidomainLogin
     */
    public function setMultidomainLogin($multidomainLogin);

}
