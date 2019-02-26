<?php

namespace Shopsys\FrameworkBundle\Form;

use CommerceGuys\Intl\Currency\CurrencyRepositoryInterface;
use Shopsys\FrameworkBundle\Form\Transformers\NumericToMoneyTransformer;
use Shopsys\FrameworkBundle\Form\Transformers\RemoveWhitespacesTransformer;
use Shopsys\FrameworkBundle\Model\Localization\Localization;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoneyTypeExtension extends AbstractTypeExtension
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\Localization
     */
    private $localization;

    /**
     * @var \CommerceGuys\Intl\Currency\CurrencyRepositoryInterface
     */
    private $intlCurrencyRepository;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Localization\Localization $localization
     * @param \CommerceGuys\Intl\Currency\CurrencyRepositoryInterface $intlCurrencyRepository
     */
    public function __construct(Localization $localization, CurrencyRepositoryInterface $intlCurrencyRepository)
    {
        $this->localization = $localization;
        $this->intlCurrencyRepository = $intlCurrencyRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new NumericToMoneyTransformer($options['scale']));
        $builder->addViewTransformer(new RemoveWhitespacesTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['money_pattern'] = $this->getPattern($options['currency']);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('currency', false);
    }

    /**
     * @return string
     */
    public function getExtendedType()
    {
        return MoneyType::class;
    }

    /**
     * Returns the pattern for this locale. Always places currency symbol after widget.
     * The pattern contains the placeholder "{{ widget }}" where the HTML tag should be inserted
     * @see \Symfony\Component\Form\Extension\Core\Type\MoneyType::getPattern()
     * @param string|bool $currency
     * @return string
     */
    private function getPattern($currency)
    {
        if (!$currency) {
            return '{{ widget }}';
        } else {
            $intlCurrency = $this->intlCurrencyRepository->get($currency, $this->localization->getLocale());

            return '{{ widget }} ' . $intlCurrency->getSymbol();
        }
    }
}
