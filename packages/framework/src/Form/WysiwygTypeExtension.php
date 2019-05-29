<?php

namespace Shopsys\FrameworkBundle\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Shopsys\FrameworkBundle\Component\Css\CssFacade;
use Shopsys\FrameworkBundle\Model\Localization\Localization;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WysiwygTypeExtension extends AbstractTypeExtension
{
    /** @access protected */
    const ALLOWED_FORMAT_TAGS = 'p;h2;h3;h4;h5;h6;pre;div;address';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Css\CssFacade
     */
    private $cssFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\Localization
     */
    private $localization;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Css\CssFacade $cssFacade
     * @param \Shopsys\FrameworkBundle\Model\Localization\Localization $localization
     */
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
                'format_tags' => static::ALLOWED_FORMAT_TAGS,
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
