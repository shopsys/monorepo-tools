<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Image\Config\ImageConfig;
use SS6\ShopBundle\Component\Image\ImageFacade;
use SS6\ShopBundle\Model\Advert\AdvertPositionList;

class ImageController extends AdminBaseController {

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
	 * @var \SS6\ShopBundle\Component\Image\ImageFacade
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

		return $this->render('@SS6Shop/Admin/Content/Image/overview.html.twig', [
			'imageEntityConfigs' => $imageEntityConfigs,
			'entityNames' => $this->getEntityNamesTranslations($imageEntityConfigs),
			'usagesByEntityAndSizeName' => $this->getImageSizeUsagesTranslations($imageEntityConfigs),
			'usagesByEntityAndTypeAndSizeName' => $this->getImageSizeWithTypeUsagesTranslations($imageEntityConfigs),
		]);
	}

	/**
	 * @param \SS6\ShopBundle\Component\Image\Config\ImageEntityConfig[] $imageEntityConfigs
	 * @return string[]
	 */
	private function getEntityNamesTranslations(array $imageEntityConfigs) {
		$names = [];
		foreach ($imageEntityConfigs as $imageEntityConfig) {
			/* @var $imageEntityConfig \SS6\ShopBundle\Component\Image\Config\ImageEntityConfig */
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
			self::ENTITY_NAME_CATEGORY => t('Kategorie'),
			self::ENTITY_NAME_PAYMENT => t('Platba'),
			self::ENTITY_NAME_PRODUCT => t('Produkt'),
			self::ENTITY_NAME_SLIDER_ITEM => t('Stránka slideru'),
			self::ENTITY_NAME_TRANSPORT => t('Doprava'),
			self::ENTITY_NAME_ADVERT => t('Reklama'),
			self::ENTITY_NAME_BRAND => t('Značka'),
		];

		if (array_key_exists($entityName, $entityNamesTranslations)) {
			return $entityNamesTranslations[$entityName];
		} else {
			return $entityName;
		}
	}

	/**
	 * @param @param \SS6\ShopBundle\Component\Image\Config\ImageEntityConfig[] $imageEntityConfigs
	 * @return string[]
	 */
	private function getImageSizeUsagesTranslations(array $imageEntityConfigs) {
		$usages = [];
		foreach ($imageEntityConfigs as $imageEntityConfig) {
			/* @var $imageEntityConfig \SS6\ShopBundle\Component\Image\Config\ImageEntityConfig */
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
					'Front-end: Objednávkový proces'
				),
			],
			self::ENTITY_NAME_PRODUCT => [
				ImageConfig::DEFAULT_SIZE_NAME => t(
					'Front-end: Hlavní obrázek na detailu produktu'
				),
				self::SIZE_NAME_GALLERY_THUMBNAIL => t(
					'Front-end: Náhledy dalších obrázků pod hlavním obrázkem na detailu produktu'
				),
				self::SIZE_NAME_LIST => t(
					'Front-end: Výpis produktů v oddělení, výpis akčního zboží'
				),
				self::SIZE_NAME_THUMBNAIL => t(
					'Front-end: Náhled v našeptávači pro vyhledávání, náhled v košíku během objednávkového procesu'
				),
			],
			self::ENTITY_NAME_SLIDER_ITEM => [
				ImageConfig::DEFAULT_SIZE_NAME => t(
					'Front-end: Slider na hlavní straně'
				),
			],
			self::ENTITY_NAME_TRANSPORT => [
				ImageConfig::DEFAULT_SIZE_NAME => t(
					'Front-end: Objednávkový proces'
				),
			],
			self::ENTITY_NAME_CATEGORY => [
			ImageConfig::DEFAULT_SIZE_NAME => t(
				'Front-end: Rozcestník kategorií'
				),
			],
			self::ENTITY_NAME_ADVERT => [
				AdvertPositionList::POSITION_HEADER => t(
					'Front-end: Reklama pod hlavičkou'
				),
				AdvertPositionList::POSITION_FOOTER => t(
					'Front-end: Reklama nad patičkou'
				),
				AdvertPositionList::POSITION_PRODUCT_LIST => t(
					'Front-end: Reklama v kategorii (nad názvem kategorie)'
				),
				AdvertPositionList::POSITION_LEFT_SIDEBAR => t(
					'Front-end: Reklama v levém panelu pod stromem kategorií'
				),
			],
			self::ENTITY_NAME_BRAND => [
				ImageConfig::DEFAULT_SIZE_NAME => t(
					'Front-end: Stránka značky'
				),
			],
		];

		if (array_key_exists($sizeName, $imageSizeUsagesTranslations[$entityName])) {
			return $imageSizeUsagesTranslations[$entityName][$sizeName];
		} else {
			return t('Není zadáno pro entitu %entityName% a rozměr %sizeName%', [
				'%entityName%' => $entityName,
				'%sizeName%' => $sizeName,
			]);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Component\Image\Config\ImageEntityConfig[] $imageEntityConfigs
	 * @return string[]
	 */
	private function getImageSizeWithTypeUsagesTranslations(array $imageEntityConfigs) {
		$usages = [];
		foreach ($imageEntityConfigs as $imageEntityConfig) {
			/* @var $imageEntityConfig \SS6\ShopBundle\Component\Image\Config\ImageEntityConfig */
			foreach ($imageEntityConfig->getSizeConfigsByTypes() as $typeName => $imageSizeConfigs) {
				foreach ($imageSizeConfigs as $imageSizeConfig) {
					/* @var $imageSizeConfig \SS6\ShopBundle\Component\Image\Config\ImageSizeConfig */
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
		return t('Není zadáno pro entitu %entityName%, typ %typeName% a rozměr %sizeName%', [
			'%entityName%' => $entityName,
			'%typeName%' => $typeName,
			'%sizeName%' => $sizeName,
		]);
	}
}
