<?php

namespace Shopsys\ShopBundle\Model\Localization\Translation\Grid;

use Shopsys\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\ShopBundle\Form\Admin\Localization\TranslationFormType;
use Shopsys\ShopBundle\Model\Localization\Translation\Grid\TranslationGridFactory;
use Shopsys\ShopBundle\Model\Localization\Translation\TranslationEditFacade;
use Symfony\Component\Form\FormFactory;

class TranslationInlineEdit extends AbstractGridInlineEdit
{
    /**
     * @var \Shopsys\ShopBundle\Model\Localization\Translation\TranslationEditFacade
     */
    private $translationEditFacade;

    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    private $formFactory;

    public function __construct(
        TranslationGridFactory $translationGridFactory,
        TranslationEditFacade $translationEditFacade,
        FormFactory $formFactory
    ) {
        parent::__construct($translationGridFactory);
        $this->translationEditFacade = $translationEditFacade;
        $this->formFactory = $formFactory;
    }

    /**
     * @param array $translationData
     * @return string
     */
    protected function createEntityAndGetId($translationData)
    {
        $message = 'Method "createEntityAndGetId" is not supported in translations.';
        throw new \Shopsys\ShopBundle\Model\Localization\Grid\Exception\NotImplementedException($message);
    }

    /**
     * @param string $translationId
     * @param array $translationData
     */
    protected function editEntity($translationId, $translationData)
    {
        $this->translationEditFacade->saveTranslation($translationId, $translationData);
    }

    /**
     * @param string $translationId
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm($translationId)
    {
        if ($translationId === null) {
            $message = 'Method "getFormDataObject" for new translation is not supported in translations.';
            throw new \Shopsys\ShopBundle\Model\Localization\Grid\Exception\NotImplementedException($message);
        }
        $translation = $this->translationEditFacade->getTranslationById($translationId);
        $locales = $this->translationEditFacade->getTranslatableLocales();

        return $this->formFactory->create(TranslationFormType::class, $translation, ['locales' => $locales]);
    }

    /**
     * @return bool
     */
    public function canAddNewRow()
    {
        return false;
    }
}
