<?php

namespace Shopsys\FrameworkBundle\Model\Transport;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Gedmo\Mapping\Annotation as Gedmo;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Shopsys\FrameworkBundle\Component\Grid\Ordering\OrderableEntityInterface;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Localization\AbstractTranslatableEntity;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Transport\Exception\TransportDomainNotFoundException;

/**
 * @ORM\Table(name="transports")
 * @ORM\Entity
 *
 * @method TransportTranslation translation(?string $locale = null)
 */
class Transport extends AbstractTranslatableEntity implements OrderableEntityInterface
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
     * @var \Doctrine\Common\Collections\ArrayCollection|\Shopsys\FrameworkBundle\Model\Transport\TransportTranslation[]
     *
     * @Prezent\Translations(targetEntity="Shopsys\FrameworkBundle\Model\Transport\TransportTranslation")
     */
    protected $translations;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection|\Shopsys\FrameworkBundle\Model\Transport\TransportDomain[]
     *
     * @ORM\OneToMany(targetEntity="Shopsys\FrameworkBundle\Model\Transport\TransportDomain", mappedBy="transport", cascade={"persist"}, fetch="EXTRA_LAZY")
     */
    protected $domains;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection|\Shopsys\FrameworkBundle\Model\Transport\TransportPrice[]
     *
     * @ORM\OneToMany(targetEntity="Shopsys\FrameworkBundle\Model\Transport\TransportPrice", mappedBy="transport", cascade={"persist"})
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
     * @var \Shopsys\FrameworkBundle\Model\Payment\Payment[]|\Doctrine\Common\Collections\Collection
     * @ManyToMany(targetEntity="Shopsys\FrameworkBundle\Model\Payment\Payment", mappedBy="transports", cascade={"persist"})
     */
    protected $payments;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\TransportData $transportData
     */
    public function __construct(TransportData $transportData)
    {
        $this->translations = new ArrayCollection();
        $this->domains = new ArrayCollection();
        $this->vat = $transportData->vat;
        $this->hidden = $transportData->hidden;
        $this->deleted = false;
        $this->setTranslations($transportData);
        $this->createDomains($transportData);
        $this->prices = new ArrayCollection();
        $this->position = static::GEDMO_SORTABLE_LAST_POSITION;
        $this->payments = new ArrayCollection();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\TransportData $transportData
     */
    public function edit(TransportData $transportData)
    {
        $this->vat = $transportData->vat;
        $this->hidden = $transportData->hidden;
        $this->setTranslations($transportData);
        $this->setDomains($transportData);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\TransportData $transportData
     */
    protected function setTranslations(TransportData $transportData)
    {
        foreach ($transportData->name as $locale => $name) {
            $this->translation($locale)->setName($name);
        }
        foreach ($transportData->description as $locale => $description) {
            $this->translation($locale)->setDescription($description);
        }
        foreach ($transportData->instructions as $locale => $instructions) {
            $this->translation($locale)->setInstructions($instructions);
        }
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
        return $this->getTransportDomain($domainId)->isEnabled();
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Shopsys\FrameworkBundle\Model\Transport\TransportPrice[]
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @return \Shopsys\FrameworkBundle\Model\Transport\TransportPrice
     */
    public function getPrice(Currency $currency)
    {
        foreach ($this->prices as $price) {
            if ($price->getCurrency() === $currency) {
                return $price;
            }
        }

        $message = 'Transport price with currency ID ' . $currency->getId()
            . ' from transport with ID ' . $this->getId() . 'not found.';
        throw new \Shopsys\FrameworkBundle\Model\Transport\Exception\TransportPriceNotFoundException($message);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\TransportPriceFactoryInterface $transportPriceFactory
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $price
     */
    public function setPrice(
        TransportPriceFactoryInterface $transportPriceFactory,
        Currency $currency,
        Money $price
    ) {
        foreach ($this->prices as $transportInputPrice) {
            if ($transportInputPrice->getCurrency() === $currency) {
                $transportInputPrice->setPrice($price);
                return;
            }
        }

        $this->prices[] = $transportPriceFactory->create($this, $currency, $price);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     */
    public function getVat()
    {
        return $this->vat;
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
     * @param \Shopsys\FrameworkBundle\Model\Transport\TransportData $transportData
     */
    protected function setDomains(TransportData $transportData)
    {
        foreach ($this->domains as $transportDomain) {
            $domainId = $transportDomain->getDomainId();
            $transportDomain->setEnabled($transportData->enabled[$domainId]);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\TransportData $transportData
     */
    protected function createDomains(TransportData $transportData)
    {
        $domainIds = array_keys($transportData->enabled);

        foreach ($domainIds as $domainId) {
            $transportDomain = new TransportDomain($this, $domainId);
            $this->domains[] = $transportDomain;
        }

        $this->setDomains($transportData);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Transport\TransportTranslation
     */
    protected function createTranslation()
    {
        return new TransportTranslation();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     */
    public function addPayment(Payment $payment)
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->addTransport($this);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment[] $payments
     */
    public function setPayments(array $payments)
    {
        foreach ($this->payments as $currentPayment) {
            if (!in_array($currentPayment, $payments, true)) {
                $this->removePayment($currentPayment);
            }
        }

        foreach ($payments as $newPayment) {
            $this->addPayment($newPayment);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     */
    public function removePayment(Payment $payment)
    {
        if ($this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            $payment->removeTransport($this);
        }
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment[]|\Doctrine\Common\Collections\Collection
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Transport\TransportDomain
     */
    protected function getTransportDomain(int $domainId)
    {
        if ($this->domains !== null) {
            foreach ($this->domains as $transportDomain) {
                if ($transportDomain->getDomainId() === $domainId) {
                    return $transportDomain;
                }
            }
        }

        throw new TransportDomainNotFoundException($this->id, $domainId);
    }
}
