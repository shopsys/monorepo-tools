<?php

namespace Shopsys\FormTypesBundle;

use Shopsys\FormTypesBundle\Domain\DomainIdsProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MultidomainType extends AbstractType
{
    /**
     * @var \Shopsys\FormTypesBundle\Domain\DomainIdsProviderInterface
     */
    private $domainIdsProvider;

    /**
     * @param \Shopsys\FormTypesBundle\Domain\DomainIdsProviderInterface $domainIdsProvider
     */
    public function __construct(DomainIdsProviderInterface $domainIdsProvider)
    {
        $this->domainIdsProvider = $domainIdsProvider;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entryOptions = $options['entry_options'];
        $entryOptions['required'] = $options['required'] && $entryOptions['required'] ?? false;
        $entryOptions['constraints'] = $entryOptions['constraints'] ?? [];

        $domainIds = $this->domainIdsProvider->getAllIds();
        foreach ($domainIds as $domainId) {
            if (array_key_exists($domainId, $options['options_by_domain_id'])) {
                $domainOptions = array_merge($entryOptions, $options['options_by_domain_id'][$domainId]);
            } else {
                $domainOptions = $entryOptions;
            }

            $builder->add($domainId, $options['entry_type'], $domainOptions);
        }
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'compound' => true,
            'entry_type' => TextType::class,
            'entry_options' => [],
            'options_by_domain_id' => [],
        ]);
    }
}
