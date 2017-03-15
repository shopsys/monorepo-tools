<?php

namespace Shopsys\ShopBundle\Form\Admin\Script;

use Shopsys\ShopBundle\Component\Transformers\ScriptPlacementToBooleanTransformer;
use Shopsys\ShopBundle\Model\Script\ScriptData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ScriptFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter script name']),
                ],
            ])
            ->add('code', TextareaType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter script code']),
                ],
            ])
            ->add($builder
                ->create('placement', CheckboxType::class, ['required' => false])
                ->addModelTransformer(new ScriptPlacementToBooleanTransformer()))
            ->add('save', SubmitType::class);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ScriptData::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
