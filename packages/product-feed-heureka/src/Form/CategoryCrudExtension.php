<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Form;

use Shopsys\Plugin\PluginCrudExtensionInterface;
use Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategory;
use Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade;
use Symfony\Component\Translation\TranslatorInterface;

class CategoryCrudExtension implements PluginCrudExtensionInterface
{
    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade
     */
    private $heurekaCategoryFacade;

    /**
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade $heurekaCategoryFacade
     */
    public function __construct(
        TranslatorInterface $translator,
        HeurekaCategoryFacade $heurekaCategoryFacade
    ) {
        $this->translator = $translator;
        $this->heurekaCategoryFacade = $heurekaCategoryFacade;
    }

    /**
     * @return string
     */
    public function getFormTypeClass()
    {
        return CategoryFormType::class;
    }

    /**
     * @return string
     */
    public function getFormLabel()
    {
        return $this->translator->trans('Heureka.cz product feed');
    }

    /**
     * @param int $categoryId
     * @return array
     */
    public function getData($categoryId)
    {
        $heurekaCategory = $this->heurekaCategoryFacade->findByCategoryId($categoryId);

        $pluginData = [];
        if ($heurekaCategory !== null) {
            $pluginData['heureka_category'] = $heurekaCategory;
        }

        return $pluginData;
    }

    /**
     * @param int $categoryId
     * @param array $data
     */
    public function saveData($categoryId, $data)
    {
        if (isset($data['heureka_category']) && $data['heureka_category'] instanceof HeurekaCategory) {
            $this->heurekaCategoryFacade->changeHeurekaCategoryForCategoryId($categoryId, $data['heureka_category']);
        } else {
            $this->heurekaCategoryFacade->removeHeurekaCategoryForCategoryId($categoryId);
        }
    }

    /**
     * @param int $categoryId
     */
    public function removeData($categoryId)
    {
        $this->heurekaCategoryFacade->removeHeurekaCategoryForCategoryId($categoryId);
    }
}
