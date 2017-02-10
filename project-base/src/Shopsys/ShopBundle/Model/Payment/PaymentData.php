<?php

namespace Shopsys\ShopBundle\Model\Payment;

use Shopsys\ShopBundle\Model\Pricing\Vat\Vat;

class PaymentData {

    /**
     * @var string[]
     */
    public $name;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Vat\Vat
     */
    public $vat;

    /**
     * @var string[]
     */
    public $description;

    /**
     * @var string[]
     */
    public $instructions;

    /**
     * @var int[]
     */
    public $domains;

    /**
     * @var int
     */
    public $hidden;

    /**
     * @var string
     */
    public $image;

    /**
     * @var \Shopsys\ShopBundle\Model\Transport\Transport[]
     */
    public $transports;

    /**
     * @var bool
     */
    public $czkRounding;

    /**
     * @param string[] $name
     * @param \Shopsys\ShopBundle\Model\Pricing\Vat\Vat|null $vat
     * @param string[] $description
     * @param string[] $instructions
     * @param bool $hidden
     * @param int[] $domains
     * @param bool $czkRounding
     */
    public function __construct(
        array $name = [],
        Vat $vat = null,
        array $description = [],
        array $instructions = [],
        $hidden = false,
        array $domains = [],
        $czkRounding = false
    ) {
        $this->name = $name;
        $this->vat = $vat;
        $this->description = $description;
        $this->instructions = $instructions;
        $this->domains = $domains;
        $this->hidden = $hidden;
        $this->transports = [];
        $this->czkRounding = $czkRounding;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
     * @param \Shopsys\ShopBundle\Model\Payment\PaymentDomain[] $paymentDomains
     */
    public function setFromEntity(Payment $payment, array $paymentDomains) {
        $this->vat = $payment->getVat();
        $this->hidden = $payment->isHidden();
        $this->czkRounding = $payment->isCzkRounding();
        $this->transports = $payment->getTransports()->toArray();

        $translations = $payment->getTranslations();
        $names = [];
        $desctiptions = [];
        $instructions = [];
        foreach ($translations as $translate) {
            $names[$translate->getLocale()] = $translate->getName();
            $desctiptions[$translate->getLocale()] = $translate->getDescription();
            $instructions[$translate->getLocale()] = $translate->getInstructions();
        }
        $this->name = $names;
        $this->description = $desctiptions;
        $this->instructions = $instructions;

        $domains = [];
        foreach ($paymentDomains as $paymentDomain) {
            $domains[] = $paymentDomain->getDomainId();
        }
        $this->domains = $domains;
    }
}
