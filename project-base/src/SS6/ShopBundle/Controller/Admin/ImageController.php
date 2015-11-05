<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Image\Config\ImageConfig;
use SS6\ShopBundle\Component\Image\ImageFacade;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\Advert\AdvertPositionList;

class ImageController extends AdminBaseController {

	const ENTITY_NAME_PAYMENT = 'payment';
	const ENTITY_NAME_PRODUCT = 'product';
	const ENTITY_NAME_SLIDER_ITEM = 'sliderItem';
	const ENTITY_NAME_TRANSPORT = 'transport';
	const ENTITY_NAME_ADVERT = 'noticer';
	const SIZE_NAME_GALLERY_THUMBNAIL = 'galleryThumbnail';
	const SIZE_NAME_LIST = 'list';
	const SIZE_NAME_THUMBNAIL = 'thumbnail';

	/**
	 * @var \SS6\ShopBundle\Component\Image\ImageFacade
	 */
	private $imageFacade;

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	public function __construct(
		Translator $translator,
		ImageFacade $imageFacade
	) {
		$this->translator = $translator;
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
			self::ENTITY_NAME_PAYMENT => $this->translator->trans('Platba'),
			self::ENTITY_NAME_PRODUCT => $this->translator->trans('Produkt'),
			self::ENTITY_NAME_SLIDER_ITEM => $this->translator->trans('Stránka slideru'),
			self::ENTITY_NAME_TRANSPORT => $this->translator->trans('Doprava'),
			self::ENTITY_NAME_ADVERT => $this->translator->trans('Reklama'),
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
				ImageConfig::DEFAULT_SIZE_NAME => $this->translator->trans(
					'Front-end: Objednávkový proces'
				),
			],
			self::ENTITY_NAME_PRODUCT => [
				ImageConfig::DEFAULT_SIZE_NAME => $this->translator->trans(
					'Front-end: Hlavní obrázek na detailu produktu'
				),
				self::SIZE_NAME_GALLERY_THUMBNAIL => $this->translator->trans(
					'Front-end: Náhledy dalších obrázků pod hlavním obrázkem na detailu produktu'
				),
				self::SIZE_NAME_LIST => $this->translator->trans(
					'Front-end: Výpis produktů v oddělení, výpis akčního zboží'
				),
				self::SIZE_NAME_THUMBNAIL => $this->translator->trans(
					'Front-end: Náhled v našeptávači pro vyhledávání, náhled v košíku během objednávkového procesu'
				),
			],
			self::ENTITY_NAME_SLIDER_ITEM => [
				ImageConfig::DEFAULT_SIZE_NAME => $this->translator->trans(
					'Front-end: Slider na hlavní straně'
				),
			],
			self::ENTITY_NAME_TRANSPORT => [
				ImageConfig::DEFAULT_SIZE_NAME => $this->translator->trans(
					'Front-end: Objednávkový proces'
				),
			],
			self::ENTITY_NAME_ADVERT => [
				AdvertPositionList::POSITION_HEADER => $this->translator->trans(
					'Front-end: Reklama pod hlavičkou'
				),
				AdvertPositionList::POSITION_FOOTER => $this->translator->trans(
					'Front-end: Reklama nad patičkou'
				),
				AdvertPositionList::POSITION_PRODUCT_LIST => $this->translator->trans(
					'Front-end: Reklama v kategorii (nad názvem kategorie)'
				),
				AdvertPositionList::POSITION_LEFT_SIDEBAR => $this->translator->trans(
					'Front-end: Reklama v levém panelu pod stromem kategorií'
				),
			],
		];

		if (array_key_exists($sizeName, $imageSizeUsagesTranslations[$entityName])) {
			return $imageSizeUsagesTranslations[$entityName][$sizeName];
		} else {
			return $this->translator->trans('Není zadáno pro entitu %entityName% a rozměr %sizeName%', [
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
		return $this->translator->trans('Není zadáno pro entitu %entityName%, typ %typeName% a rozměr %sizeName%', [
			'%entityName%' => $entityName,
			'%typeName%' => $typeName,
			'%sizeName%' => $sizeName,
		]);
	}
}
