<?php

namespace Shopsys\ShopBundle\Model\Pricing\Currency;

use Doctrine\ORM\EntityManager;

class CurrencyRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getCurrencyRepository()
    {
        return $this->em->getRepository(Currency::class);
    }

    /**
     * @param int $currencyId
     * @return \Shopsys\ShopBundle\Model\Pricing\Currency\Currency|null
     */
    public function findById($currencyId)
    {
        return $this->getCurrencyRepository()->find($currencyId);
    }

    /**
     * @param string $code
     * @return \Shopsys\ShopBundle\Model\Pricing\Currency\Currency|null
     */
    public function findByCode($code)
    {
        return $this->getCurrencyRepository()->findOneBy([
            'code' => $code,
        ]);
    }

    /**
     * @param int $currencyId
     * @return \Shopsys\ShopBundle\Model\Pricing\Currency\Currency
     */
    public function getById($currencyId)
    {
        $currency = $this->findById($currencyId);

        if ($currency === null) {
            $message = 'Currency with ID ' . $currencyId . ' not found.';
            throw new \Shopsys\ShopBundle\Model\Pricing\Currency\Exception\CurrencyNotFoundException($message);
        }

        return $currency;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Currency\Currency[]
     */
    public function getAll()
    {
        return $this->getCurrencyRepository()->findAll();
    }
}
