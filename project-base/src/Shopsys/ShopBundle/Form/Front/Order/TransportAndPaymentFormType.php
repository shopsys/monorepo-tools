<?php

namespace Shopsys\ShopBundle\Form\Front\Order;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Order\OrderData;
use Shopsys\ShopBundle\Model\Payment\Payment;
use Shopsys\ShopBundle\Model\Transport\Transport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ExecutionContextInterface;

class TransportAndPaymentFormType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Model\Transport\Transport[]
     */
    private $transports;

    /**
     * @var \Shopsys\ShopBundle\Model\Payment\Payment[]
     */
    private $payments;

    /**
     * @param \Shopsys\ShopBundle\Model\Transport\Transport[]$transports
     * @param \Shopsys\ShopBundle\Model\Payment\Payment[] $payments
     */
    public function __construct(array $transports, array $payments)
    {
        $this->transports = $transports;
        $this->payments = $payments;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transport', FormType::SINGLE_CHECKBOX_CHOICE, [
                'choice_list' => new ObjectChoiceList($this->transports, 'name', [], null, 'id'),
                'constraints' => [
                    new Constraints\NotNull(['message' => 'Please choose shipping type']),
                ],
                'invalid_message' => 'Please choose shipping type',
            ])
            ->add('payment', FormType::SINGLE_CHECKBOX_CHOICE, [
                'choice_list' => new ObjectChoiceList($this->payments, 'name', [], null, 'id'),
                'constraints' => [
                    new Constraints\NotNull(['message' => 'Please choose payment type']),
                ],
                'invalid_message' => 'Please choose payment type',
            ])
            ->add('save', FormType::SUBMIT);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
            'constraints' => [
                new Constraints\Callback([$this, 'validateTransportPaymentRelation']),
            ],
        ]);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\OrderData $orderData
     * @param \Symfony\Component\Validator\ExecutionContextInterface $context
     */
    public function validateTransportPaymentRelation(OrderData $orderData, ExecutionContextInterface $context)
    {
        $payment = $orderData->payment;
        $transport = $orderData->transport;

        $relationExists = false;
        if ($payment instanceof Payment && $transport instanceof Transport) {
            if ($payment->getTransports()->contains($transport)) {
                $relationExists = true;
            }
        }

        if (!$relationExists) {
            $context->addViolation('Please choose a valid combination of transport and payment');
        }
    }
}
