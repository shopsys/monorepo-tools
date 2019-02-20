<?php

namespace Shopsys\FrameworkBundle\Model\Pricing;

use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation;
use Shopsys\FrameworkBundle\Model\Transport\Transport;
use Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation;

class InputPriceRecalculator
{
    const BATCH_SIZE = 500;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation
     */
    protected $inputPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation
     */
    protected $paymentPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation
     */
    protected $transportPriceCalculation;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation $inputPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation $paymentPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation $transportPriceCalculation
     */
    public function __construct(
        EntityManagerInterface $em,
        InputPriceCalculation $inputPriceCalculation,
        PaymentPriceCalculation $paymentPriceCalculation,
        TransportPriceCalculation $transportPriceCalculation
    ) {
        $this->em = $em;
        $this->inputPriceCalculation = $inputPriceCalculation;
        $this->paymentPriceCalculation = $paymentPriceCalculation;
        $this->transportPriceCalculation = $transportPriceCalculation;
    }

    public function recalculateToInputPricesWithoutVat()
    {
        $this->recalculateInputPriceForNewType(PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT);
    }

    public function recalculateToInputPricesWithVat()
    {
        $this->recalculateInputPriceForNewType(PricingSetting::INPUT_PRICE_TYPE_WITH_VAT);
    }

    /**
     * @param string $newInputPriceType
     */
    protected function recalculateInputPriceForNewType($newInputPriceType)
    {
        $this->recalculateTransportsInputPriceForNewType($newInputPriceType);
        $this->recalculatePaymentsInputPriceForNewType($newInputPriceType);
    }

    /**
     * @param string $toInputPriceType
     */
    protected function recalculatePaymentsInputPriceForNewType($toInputPriceType)
    {
        $query = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Payment::class, 'p')
            ->getQuery();

        $this->batchProcessQuery($query, function (Payment $payment) use ($toInputPriceType) {
            foreach ($payment->getPrices() as $paymentInputPrice) {
                $paymentPrice = $this->paymentPriceCalculation->calculateIndependentPrice(
                    $payment,
                    $paymentInputPrice->getCurrency()
                );

                $newInputPrice = $this->inputPriceCalculation->getInputPrice(
                    $toInputPriceType,
                    $paymentPrice->getPriceWithVat()->toValue(),
                    $payment->getVat()->getPercent()
                );

                $paymentInputPrice->setPrice(Money::fromValue($newInputPrice));
            }
        });
    }

    /**
     * @param string $toInputPriceType
     */
    protected function recalculateTransportsInputPriceForNewType($toInputPriceType)
    {
        $query = $this->em->createQueryBuilder()
            ->select('t')
            ->from(Transport::class, 't')
            ->getQuery();

        $this->batchProcessQuery($query, function (Transport $transport) use ($toInputPriceType) {
            foreach ($transport->getPrices() as $transportInputPrice) {
                $transportPrice = $this->transportPriceCalculation->calculateIndependentPrice(
                    $transport,
                    $transportInputPrice->getCurrency()
                );

                $newInputPrice = $this->inputPriceCalculation->getInputPrice(
                    $toInputPriceType,
                    $transportPrice->getPriceWithVat()->toValue(),
                    $transport->getVat()->getPercent()
                );

                $transportInputPrice->setPrice(Money::fromValue($newInputPrice));
            }
        });
    }

    /**
     * @param \Doctrine\ORM\Query $query
     * @param \Closure $callback
     */
    protected function batchProcessQuery(Query $query, Closure $callback)
    {
        $iteration = 0;

        foreach ($query->iterate() as $row) {
            $iteration++;
            $object = $row[0];

            $callback($object);

            if (($iteration % self::BATCH_SIZE) == 0) {
                $this->em->flush();
            }
        }

        $this->em->flush();
    }
}
