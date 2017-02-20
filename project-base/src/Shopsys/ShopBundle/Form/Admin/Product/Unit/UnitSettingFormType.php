<?php

namespace Shopsys\ShopBundle\Form\Admin\Product\Unit;

use Shopsys\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class UnitSettingFormType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\Unit\Unit[]
     */
    private $units;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Unit\Unit[] $units
     */
    public function __construct(array $units)
    {
        $this->units = $units;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('defaultUnit', FormType::CHOICE, [
                'required' => true,
                'choice_list' => new ObjectChoiceList($this->units, 'name', [], null, 'id'),
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please choose default unit']),
                ],
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
        ]);
    }
}
