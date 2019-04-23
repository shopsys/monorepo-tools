<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\String\TransformString;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ImageDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    const IMAGES_TABLE_NAME = 'images';

    const IMAGE_TYPE = 'jpg';

    /**
     * @var string
     */
    protected $dataFixturesImagesDirectory;

    /**
     * @var string
     */
    protected $targetDomainImagesDirectory;

    /**
     * @var string
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $targetImagesDirectory;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $localFilesystem;

    /**
     * @var \League\Flysystem\MountManager
     */
    protected $mountManager;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @param mixed $dataFixturesImagesDirectory
     * @param mixed $targetImagesDirectory
     * @param mixed $targetDomainImagesDirectory
     * @param \League\Flysystem\FilesystemInterface $filesystem
     * @param \Symfony\Component\Filesystem\Filesystem $symfonyFilesystem
     * @param \League\Flysystem\MountManager $mountManager
     * @param \Doctrine\ORM\EntityManagerInterface $em
     */
    public function __construct(
        $dataFixturesImagesDirectory,
        $targetImagesDirectory,
        $targetDomainImagesDirectory,
        FilesystemInterface $filesystem,
        Filesystem $symfonyFilesystem,
        MountManager $mountManager,
        EntityManagerInterface $em
    ) {
        $this->dataFixturesImagesDirectory = $dataFixturesImagesDirectory;
        $this->targetDomainImagesDirectory = $targetDomainImagesDirectory;
        $this->targetImagesDirectory = $targetImagesDirectory;
        $this->filesystem = $filesystem;
        $this->localFilesystem = $symfonyFilesystem;
        $this->mountManager = $mountManager;
        $this->em = $em;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->truncateImagesFromDb();

        if (file_exists($this->dataFixturesImagesDirectory)) {
            $this->moveFilesFromLocalFilesystemToFilesystem($this->dataFixturesImagesDirectory . 'domain/', $this->targetDomainImagesDirectory);
            $this->moveFilesFromLocalFilesystemToFilesystem($this->dataFixturesImagesDirectory, $this->targetImagesDirectory);
            $this->processDbImagesChanges();
        }
    }

    protected function processDbImagesChanges()
    {
        $this->processBrandsImages();
        $this->processCategoriesImages();
        $this->processPaymentsImages();
        $this->processTransportsImages();
        $this->processProductsImages();
        $this->processSliderItemsImages();
        $this->restartImagesIdsDbSequence();
    }

    protected function processBrandsImages()
    {
        $brandsImagesData = [
            79 => BrandDataFixture::BRAND_APPLE,
            80 => BrandDataFixture::BRAND_CANON,
            81 => BrandDataFixture::BRAND_LG,
            82 => BrandDataFixture::BRAND_PHILIPS,
            83 => BrandDataFixture::BRAND_SENCOR,
            84 => BrandDataFixture::BRAND_A4TECH,
            85 => BrandDataFixture::BRAND_BROTHER,
            86 => BrandDataFixture::BRAND_VERBATIM,
            87 => BrandDataFixture::BRAND_DLINK,
            88 => BrandDataFixture::BRAND_DEFENDER,
            89 => BrandDataFixture::BRAND_DELONGHI,
            90 => BrandDataFixture::BRAND_GENIUS,
            91 => BrandDataFixture::BRAND_GIGABYTE,
            92 => BrandDataFixture::BRAND_HP,
            93 => BrandDataFixture::BRAND_HTC,
            94 => BrandDataFixture::BRAND_JURA,
            95 => BrandDataFixture::BRAND_LOGITECH,
            96 => BrandDataFixture::BRAND_MICROSOFT,
            97 => BrandDataFixture::BRAND_SAMSUNG,
            98 => BrandDataFixture::BRAND_SONY,
            99 => BrandDataFixture::BRAND_ORAVA,
            100 => BrandDataFixture::BRAND_OLYMPUS,
            101 => BrandDataFixture::BRAND_HYUNDAI,
            102 => BrandDataFixture::BRAND_NIKON,
        ];

        foreach ($brandsImagesData as $imageId => $brandName) {
            $brand = $this->getReference($brandName);
            /* @var $brand \Shopsys\FrameworkBundle\Model\Product\Brand\Brand */

            $this->saveImageIntoDb($brand->getId(), 'brand', $imageId);
        }
    }

    protected function processCategoriesImages()
    {
        $categoriesImagesData = [
            68 => CategoryDataFixture::CATEGORY_ELECTRONICS,
            69 => CategoryDataFixture::CATEGORY_TV,
            70 => CategoryDataFixture::CATEGORY_PHOTO,
            71 => CategoryDataFixture::CATEGORY_PRINTERS,
            72 => CategoryDataFixture::CATEGORY_PC,
            73 => CategoryDataFixture::CATEGORY_PHONES,
            74 => CategoryDataFixture::CATEGORY_COFFEE,
            75 => CategoryDataFixture::CATEGORY_BOOKS,
            76 => CategoryDataFixture::CATEGORY_TOYS,
            77 => CategoryDataFixture::CATEGORY_GARDEN_TOOLS,
            78 => CategoryDataFixture::CATEGORY_FOOD,
        ];

        foreach ($categoriesImagesData as $imageId => $categoryName) {
            $category = $this->getReference($categoryName);
            /* @var $category \Shopsys\FrameworkBundle\Model\Category\Category */

            $this->saveImageIntoDb($category->getId(), 'category', $imageId);
        }
    }

    protected function processPaymentsImages()
    {
        $paymentsImagesData = [
            53 => PaymentDataFixture::PAYMENT_CARD,
            55 => PaymentDataFixture::PAYMENT_CASH_ON_DELIVERY,
            54 => PaymentDataFixture::PAYMENT_CASH,
        ];

        foreach ($paymentsImagesData as $imageId => $paymentName) {
            $payment = $this->getReference($paymentName);
            /* @var $payment \Shopsys\FrameworkBundle\Model\Payment\Payment */

            $this->saveImageIntoDb($payment->getId(), 'payment', $imageId);
        }
    }

    protected function processTransportsImages()
    {
        $transportsImagesData = [
            56 => TransportDataFixture::TRANSPORT_CZECH_POST,
            57 => TransportDataFixture::TRANSPORT_PPL,
            58 => TransportDataFixture::TRANSPORT_PERSONAL,
        ];

        foreach ($transportsImagesData as $imageId => $transportName) {
            $transport = $this->getReference($transportName);
            /* @var $transport \Shopsys\FrameworkBundle\Model\Transport\Transport */

            $this->saveImageIntoDb($transport->getId(), 'transport', $imageId);
        }
    }

    protected function processProductsImages()
    {
        $productsIdsWithImageIdSameAsProductId = [
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
            11, 12, 13, 14, 15, 16, 17, 18,
            19, 20, 21, 22, 24, 25, 26, 27,
            28, 29, 30, 31, 32, 33, 35, 36,
            37, 38, 39, 41, 42, 43, 44, 45,
            46, 47, 48, 49, 51, 52,
        ];

        $specificProductsIdsIndexedByImagesIds = [
            64 => 1,
            67 => 5,
        ];

        foreach ($productsIdsWithImageIdSameAsProductId as $productId) {
            $this->saveImageIntoDb($productId, 'product', $productId);
        }

        foreach ($specificProductsIdsIndexedByImagesIds as $imageId => $productId) {
            $this->saveImageIntoDb($productId, 'product', $imageId);
        }
    }

    protected function processSliderItemsImages()
    {
        $imagesIdsIndexedBySliderItemsIds = [
            1 => 59,
            2 => 60,
            3 => 61,
        ];

        foreach ($imagesIdsIndexedBySliderItemsIds as $sliderItemId => $imageId) {
            $this->saveImageIntoDb($sliderItemId, 'sliderItem', $imageId);
        }
    }

    /**
     * @param int $entityId
     * @param string $entityName
     * @param int $imageId
     */
    protected function saveImageIntoDb(int $entityId, string $entityName, int $imageId)
    {
        $query = $this->em->createNativeQuery(
            'INSERT INTO images (id, entity_name, entity_id, type, extension, position, modified_at)
            VALUES (:id, :entity_name, :entity_id, :type, :extension, :position, :modified_at)',
            new ResultSetMapping()
        );

        $query->execute([
            'id' => $imageId,
            'entity_name' => $entityName,
            'entity_id' => $entityId,
            'type' => null,
            'extension' => self::IMAGE_TYPE,
            'position' => null,
            'modified_at' => '2015-04-16 11:36:06',
        ]);
    }

    /**
     * @param string $origin
     * @param string $target
     */
    protected function moveFilesFromLocalFilesystemToFilesystem(string $origin, string $target)
    {
        $finder = new Finder();
        $finder->files()->in($origin);
        foreach ($finder as $file) {
            $filepath = TransformString::removeDriveLetterFromPath($file->getPathname());

            if ($this->localFilesystem->exists($filepath)) {
                $newFilepath = $target . $file->getRelativePathname();

                if ($this->filesystem->has($newFilepath)) {
                    $this->filesystem->delete($newFilepath);
                }
                $this->mountManager->copy('local://' . $filepath, 'main://' . $newFilepath);
            }
        }
    }

    protected function truncateImagesFromDb()
    {
        $this->em->createNativeQuery('TRUNCATE TABLE ' . self::IMAGES_TABLE_NAME, new ResultSetMapping())->execute();
    }

    protected function restartImagesIdsDbSequence()
    {
        $this->em->createNativeQuery('ALTER SEQUENCE images_id_seq RESTART WITH 103', new ResultSetMapping())->execute();
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            BrandDataFixture::class,
            CategoryDataFixture::class,
            PaymentDataFixture::class,
            TransportDataFixture::class,
            ProductDataFixture::class,
            SliderItemDataFixture::class,
        ];
    }
}
