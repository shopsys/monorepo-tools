<?php

namespace Shopsys\ShopBundle\Form\Admin\Module;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Module\ModuleList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ModulesFormType extends AbstractType
{

    const MODULES_SUBFORM_NAME = 'modules';

    /**
     * @var \Shopsys\ShopBundle\Model\Module\ModuleList
     */
    private $moduleList;

    public function __construct(ModuleList $moduleList) {
        $this->moduleList = $moduleList;
    }

    /**
     * @return string
     */
    public function getName() {
        return 'modules_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add(self::MODULES_SUBFORM_NAME, FormType::FORM)
            ->add('save', FormType::SUBMIT);

        foreach ($this->moduleList->getTranslationsIndexedByValue() as $moduleName => $moduleTranslation) {
            $builder->get(self::MODULES_SUBFORM_NAME)
                ->add($moduleName, FormType::YES_NO, [
                    'label' => $moduleTranslation,
                ]);
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
