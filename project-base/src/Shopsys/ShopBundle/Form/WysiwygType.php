<?php

namespace Shopsys\ShopBundle\Form;

use Shopsys\ShopBundle\Component\Css\CssFacade;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WysiwygType extends AbstractTypeExtension
{
    /**
     * @var \Shopsys\ShopBundle\Component\Css\CssFacade
     */
    private $cssFacade;

    public function __construct(CssFacade $cssFacade)
    {
        $this->cssFacade = $cssFacade;
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
            ],
        ]);
    }

    /**
     * @return string
     */
    public function getExtendedType()
    {
        return 'ckeditor';
    }
}
