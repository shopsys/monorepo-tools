<?php

namespace Shopsys\FrameworkBundle\Form;

use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Order\OrderFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderListType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderFacade
     */
    private $orderFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderFacade $orderFacade
     */
    public function __construct(OrderFacade $orderFacade)
    {
        $this->orderFacade = $orderFacade;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('user')
            ->setAllowedTypes('user', User::class)
            ->setDefaults([
                'mapped' => false,
            ]);
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['orders'] = $this->orderFacade->getCustomerOrderList($options['user']);
    }

    /**
     * @return null|string
     */
    public function getParent()
    {
        return FormType::class;
    }
}
