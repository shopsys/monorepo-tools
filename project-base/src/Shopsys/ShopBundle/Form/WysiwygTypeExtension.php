<?php

namespace Shopsys\ShopBundle\Form;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Shopsys\ShopBundle\Component\Css\CssFacade;
use Shopsys\ShopBundle\Model\Localization\Localization;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WysiwygTypeExtension extends AbstractTypeExtension
{
    /**
     * @var \Shopsys\ShopBundle\Component\Css\CssFacade
     */
    private $cssFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Localization\Localization
     */
    private $localization;

    public function __construct(CssFacade $cssFacade, Localization $localization)
    {
        $this->cssFacade = $cssFacade;
        $this->localization = $localization;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $cssVersion = $this->cssFacade->getCssVersion();

        $resolver->setDefaults([
            'config' => [
                'contentsCss' => [
                    'assets/admin/styles/wysiwyg_' . $cssVersion . '.css',
                ],
                'language' => $this->localization->getLocale(),
            ],
        ]);
    }

    /**
     * @return string
     */
    public function getExtendedType()
    {
        return CKEditorType::class;
    }
}
