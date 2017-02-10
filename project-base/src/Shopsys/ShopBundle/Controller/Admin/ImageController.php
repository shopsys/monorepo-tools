<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Image\Config\ImageConfig;
use Shopsys\ShopBundle\Component\Image\ImageFacade;
use Shopsys\ShopBundle\Model\Advert\AdvertPositionList;

class ImageController extends AdminBaseController
{

    const ENTITY_NAME_PAYMENT = 'payment';
    const ENTITY_NAME_PRODUCT = 'product';
    const ENTITY_NAME_SLIDER_ITEM = 'sliderItem';
    const ENTITY_NAME_TRANSPORT = 'transport';
    const ENTITY_NAME_ADVERT = 'noticer';
    const ENTITY_NAME_CATEGORY = 'category';
    const ENTITY_NAME_BRAND = 'brand';
    const SIZE_NAME_GALLERY_THUMBNAIL = 'galleryThumbnail';
    const SIZE_NAME_LIST = 'list';
    const SIZE_NAME_THUMBNAIL = 'thumbnail';

    /**
     * @var \Shopsys\ShopBundle\Component\Image\ImageFacade
     */
    private $imageFacade;

    public function __construct(ImageFacade $imageFacade) {
        $this->imageFacade = $imageFacade;
    }

    /**
     * @Route("/image/overview/")
     */
    public function overviewAction() {
        $imageEntityConfigs = $this->imageFacade->getAllImageEntityConfigsByClass();

        return $this->render('@ShopsysShop/Admin/Content/Image/overview.html.twig', [
            'imageEntityConfigs' => $imageEntityConfigs,
            'entityNames' => $this->getEntityNamesTranslations($imageEntityConfigs),
            'usagesByEntityAndSizeName' => $this->getImageSizeUsagesTranslations($imageEntityConfigs),
            'usagesByEntityAndTypeAndSizeName' => $this->getImageSizeWithTypeUsagesTranslations($imageEntityConfigs),
        ]);
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Image\Config\ImageEntityConfig[] $imageEntityConfigs
     * @return string[]
     */
    private function getEntityNamesTranslations(array $imageEntityConfigs) {
        $names = [];
        foreach ($imageEntityConfigs as $imageEntityConfig) {
            /* @var $imageEntityConfig \Shopsys\ShopBundle\Component\Image\Config\ImageEntityConfig */
            $names[$imageEntityConfig->getEntityName()] = $this->getEntityNameTranslation($imageEntityConfig->getEntityName());
        }
        return $names;
    }

    /**
     * @param string $entityName
     * @return string
     */
    private function getEntityNameTranslation($entityName) {
        $entityNamesTranslations = [
            self::ENTITY_NAME_CATEGORY => t('Category'),
            self::ENTITY_NAME_PAYMENT => t('Payment'),
            self::ENTITY_NAME_PRODUCT => t('Product'),
            self::ENTITY_NAME_SLIDER_ITEM => t('Slider page'),
            self::ENTITY_NAME_TRANSPORT => t('Shipping'),
            self::ENTITY_NAME_ADVERT => t('Advertising'),
            self::ENTITY_NAME_BRAND => t('Brand'),
        ];

        if (array_key_exists($entityName, $entityNamesTranslations)) {
            return $entityNamesTranslations[$entityName];
        } else {
            return $entityName;
        }
    }

    /**
     * @param @param \Shopsys\ShopBundle\Component\Image\Config\ImageEntityConfig[] $imageEntityConfigs
     * @return string[]
     */
    private function getImageSizeUsagesTranslations(array $imageEntityConfigs) {
        $usages = [];
        foreach ($imageEntityConfigs as $imageEntityConfig) {
            /* @var $imageEntityConfig \Shopsys\ShopBundle\Component\Image\Config\ImageEntityConfig */
            foreach ($imageEntityConfig->getSizeConfigs() as $imageSizeConfig) {
                $entityName = $imageEntityConfig->getEntityName();
                $sizeName = $imageSizeConfig->getName();
                if ($sizeName === null) {
                    $sizeName = ImageConfig::DEFAULT_SIZE_NAME;
                }
                $usages[$entityName][$sizeName] = $this->getImageSizeUsageTranslation($entityName, $sizeName);
            }
        }

        return $usages;
    }

    /**
     * @param string $entityName
     * @param string $sizeName
     * @return string
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function getImageSizeUsageTranslation($entityName, $sizeName) {
        $imageSizeUsagesTranslations = [
            self::ENTITY_NAME_PAYMENT => [
                ImageConfig::DEFAULT_SIZE_NAME => t(
                    'Front-end: Ordering process'
                ),
            ],
            self::ENTITY_NAME_PRODUCT => [
                ImageConfig::DEFAULT_SIZE_NAME => t(
                    'Front-end: Main image in product detail'
                ),
                self::SIZE_NAME_GALLERY_THUMBNAIL => t(
                    'Front-end: Thumbnail of images under the main picture in product detail'
                ),
                self::SIZE_NAME_LIST => t(
                    'Front-end: Listing of products in section, listing of products on special offer'
                ),
                self::SIZE_NAME_THUMBNAIL => t(
                    'Front-end: Preview in autocomplete for search, preview in cart during ordering process'
                ),
            ],
            self::ENTITY_NAME_SLIDER_ITEM => [
                ImageConfig::DEFAULT_SIZE_NAME => t(
                    'Front-end: Main page slider'
                ),
            ],
            self::ENTITY_NAME_TRANSPORT => [
                ImageConfig::DEFAULT_SIZE_NAME => t(
                    'Front-end: Ordering process'
                ),
            ],
            self::ENTITY_NAME_CATEGORY => [
            ImageConfig::DEFAULT_SIZE_NAME => t(
                'Front-end: Category guidepost'
                ),
            ],
            self::ENTITY_NAME_ADVERT => [
                AdvertPositionList::POSITION_HEADER => t(
                    'Front-end: Advertising under heading'
                ),
                AdvertPositionList::POSITION_FOOTER => t(
                    'Front-end: Advertising above footer'
                ),
                AdvertPositionList::POSITION_PRODUCT_LIST => t(
                    'Front-end: Advertising in category (above the category name)'
                ),
                AdvertPositionList::POSITION_LEFT_SIDEBAR => t(
                    'Front-end: Advertising in the left panel under the category tree'
                ),
            ],
            self::ENTITY_NAME_BRAND => [
                ImageConfig::DEFAULT_SIZE_NAME => t(
                    'Front-end: Brand page'
                ),
            ],
        ];

        if (array_key_exists($sizeName, $imageSizeUsagesTranslations[$entityName])) {
            return $imageSizeUsagesTranslations[$entityName][$sizeName];
        } else {
            return t('Not specified for entity %entityName% and size %sizeName%', [
                '%entityName%' => $entityName,
                '%sizeName%' => $sizeName,
            ]);
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Image\Config\ImageEntityConfig[] $imageEntityConfigs
     * @return string[]
     */
    private function getImageSizeWithTypeUsagesTranslations(array $imageEntityConfigs) {
        $usages = [];
        foreach ($imageEntityConfigs as $imageEntityConfig) {
            /* @var $imageEntityConfig \Shopsys\ShopBundle\Component\Image\Config\ImageEntityConfig */
            foreach ($imageEntityConfig->getSizeConfigsByTypes() as $typeName => $imageSizeConfigs) {
                foreach ($imageSizeConfigs as $imageSizeConfig) {
                    /* @var $imageSizeConfig \Shopsys\ShopBundle\Component\Image\Config\ImageSizeConfig */
                    $entityName = $imageEntityConfig->getEntityName();
                    $sizeName = $imageSizeConfig->getName();
                    if ($sizeName === null) {
                        $sizeName = ImageConfig::DEFAULT_SIZE_NAME;
                    }
                    $usages[$entityName][$typeName][$sizeName] = $this->getImageSizeWithTypeUsageTranslation(
                        $entityName,
                        $typeName,
                        $sizeName
                    );
                }
            }
        }

        return $usages;
    }

    /**
     * @param string $entityName
     * @param string $typeName
     * @param string $sizeName
     * @return string
     */
    private function getImageSizeWithTypeUsageTranslation($entityName, $typeName, $sizeName) {
        return t('Not specified for entity %entityName%, type %typeName% and size %sizeName%', [
            '%entityName%' => $entityName,
            '%typeName%' => $typeName,
            '%sizeName%' => $sizeName,
        ]);
    }
}
