<?php

namespace Shopsys\FrameworkBundle\Model\Payment;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Shopsys\FrameworkBundle\Component\Grid\Ordering\OrderableEntityInterface;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Localization\AbstractTranslatableEntity;
use Shopsys\FrameworkBundle\Model\Payment\Exception\PaymentDomainNotFoundException;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Transport\Transport;

/**
 * @ORM\Table(name="payments")
 * @ORM\Entity
 *
 * @method PaymentTranslation translation(?string $locale = null)
 */
class Payment extends AbstractTranslatableEntity implements OrderableEntityInterface
{
    protected const GEDMO_SORTABLE_LAST_POSITION = -1;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentTranslation[]|\Doctrine\Common\Collections\Collection
     *
     * @Prezent\Translations(targetEntity="Shopsys\FrameworkBundle\Model\Payment\PaymentTranslation")
     */
    protected $translations;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentPrice[]|\Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Shopsys\FrameworkBundle\Model\Payment\PaymentPrice", mappedBy="payment", cascade={"persist"})
     */
    protected $prices;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $vat;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\Transport[]|\Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Shopsys\FrameworkBundle\Model\Transport\Transport", inversedBy="payments", cascade={"persist"})
     * @ORM\JoinTable(name="payments_transports")
     */
    protected $transports;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $hidden;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $deleted;

    /**
     * @var int|null
     *
     * @Gedmo\SortablePosition
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $position;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $czkRounding;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentDomain[]|\Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Shopsys\FrameworkBundle\Model\Payment\PaymentDomain", mappedBy="payment", cascade={"persist"}, fetch="EXTRA_LAZY")
     */
    protected $domains;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentData $paymentData
     */
    public function __construct(PaymentData $paymentData)
    {
        $this->translations = new ArrayCollection();
        $this->domains = new ArrayCollection();
        $this->vat = $paymentData->vat;
        $this->transports = new ArrayCollection();
        $this->hidden = $paymentData->hidden;
        $this->deleted = false;
        $this->setTranslations($paymentData);
        $this->createDomains($paymentData);
        $this->prices = new ArrayCollection();
        $this->czkRounding = $paymentData->czkRounding;
        $this->position = static::GEDMO_SORTABLE_LAST_POSITION;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     */
    public function addTransport(Transport $transport)
    {
        if (!$this->transports->contains($transport)) {
            $this->transports->add($transport);
            $transport->addPayment($this);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport[] $transports
     */
    public function setTransports(array $transports)
    {
        foreach ($this->transports as $currentTransport) {
            if (!in_array($currentTransport, $transports, true)) {
                $this->removeTransport($currentTransport);
            }
        }

        foreach ($transports as $newTransport) {
            $this->addTransport($newTransport);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     */
    public function removeTransport(Transport $transport)
    {
        if ($this->transports->contains($transport)) {
            $this->transports->removeElement($transport);
            $transport->removePayment($this);
        }
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Transport\Transport[]
     */
    public function getTransports()
    {
        return $this->transports->toArray();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentData $paymentData
     */
    protected function setTranslations(PaymentData $paymentData)
    {
        foreach ($paymentData->name as $locale => $name) {
            $this->translation($locale)->setName($name);
        }
        foreach ($paymentData->description as $locale => $description) {
            $this->translation($locale)->setDescription($description);
        }
        foreach ($paymentData->instructions as $locale => $instructions) {
            $this->translation($locale)->setInstructions($instructions);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentData $paymentData
     */
    public function edit(PaymentData $paymentData)
    {
        $this->vat = $paymentData->vat;
        $this->hidden = $paymentData->hidden;
        $this->czkRounding = $paymentData->czkRounding;
        $this->setTranslations($paymentData);
        $this->setDomains($paymentData);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentPriceFactoryInterface $paymentPriceFactory
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $price
     */
    public function setPrice(
        PaymentPriceFactoryInterface $paymentPriceFactory,
        Currency $currency,
        Money $price
    ) {
        foreach ($this->prices as $paymentInputPrice) {
            if ($paymentInputPrice->getCurrency() === $currency) {
                $paymentInputPrice->setPrice($price);
                return;
            }
        }

        $this->prices->add($paymentPriceFactory->create($this, $currency, $price));
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string|null $locale
     * @return string
     */
    public function getName($locale = null)
    {
        return $this->translation($locale)->getName();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\PaymentPrice[]
     */
    public function getPrices()
    {
        return $this->prices->toArray();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @return \Shopsys\FrameworkBundle\Model\Payment\PaymentPrice
     */
    public function getPrice(Currency $currency)
    {
        foreach ($this->prices as $price) {
            if ($price->getCurrency() === $currency) {
                return $price;
            }
        }

        $message = 'Payment price with currency ID ' . $currency->getId() . ' from payment with ID ' . $this->getId() . 'not found.';
        throw new \Shopsys\FrameworkBundle\Model\Payment\Exception\PaymentPriceNotFoundException($message);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param string|null $locale
     * @return string|null
     */
    public function getDescription($locale = null)
    {
        return $this->translation($locale)->getDescription();
    }

    /**
     * @param string|null $locale
     * @return string|null
     */
    public function getInstructions($locale = null)
    {
        return $this->translation($locale)->getInstructions();
    }

    /**
     * @param int $domainId
     * @return bool
     */
    public function isEnabled(int $domainId)
    {
        return $this->getPaymentDomain($domainId)->isEnabled();
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    public function markAsDeleted()
    {
        $this->deleted = true;
        $this->transports->clear();
    }

    /**
     * @return int|null
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return bool
     */
    public function isCzkRounding()
    {
        return $this->czkRounding;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\PaymentTranslation
     */
    protected function createTranslation()
    {
        return new PaymentTranslation();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentData $paymentData
     */
    protected function setDomains(PaymentData $paymentData)
    {
        foreach ($this->domains as $paymentDomain) {
            $domainId = $paymentDomain->getDomainId();
            $paymentDomain->setEnabled($paymentData->enabled[$domainId]);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentData $paymentData
     */
    protected function createDomains(PaymentData $paymentData)
    {
        $domainIds = array_keys($paymentData->enabled);

        foreach ($domainIds as $domainId) {
            $paymentDomain = new PaymentDomain($this, $domainId);
            $this->domains->add($paymentDomain);
        }

        $this->setDomains($paymentData);
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Payment\PaymentDomain
     */
    protected function getPaymentDomain(int $domainId)
    {
        foreach ($this->domains as $paymentDomain) {
            if ($paymentDomain->getDomainId() === $domainId) {
                return $paymentDomain;
            }
        }

        throw new PaymentDomainNotFoundException($this->id, $domainId);
    }
}
