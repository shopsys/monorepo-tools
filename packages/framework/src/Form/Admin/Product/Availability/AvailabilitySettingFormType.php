<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Product\Availability;

use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class AvailabilitySettingFormType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade
     */
    private $availabilityFacade;

    public function __construct(AvailabilityFacade $availabilityFacade)
    {
        $this->availabilityFacade = $availabilityFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('defaultInStockAvailability', ChoiceType::class, [
                'required' => true,
                'choices' => $this->availabilityFacade->getAll(),
                'choice_label' => 'name',
                'choice_value' => 'id',
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please choose availability for stock products']),
                ],
            ])
            ->add('save', SubmitType::class);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
