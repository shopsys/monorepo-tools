<?php

namespace Shopsys\ShopBundle\Component\Image;

use Shopsys\ShopBundle\Component\Image\Config\ImageConfig;
use Shopsys\ShopBundle\Component\Image\ImageLocator;
use Symfony\Component\Filesystem\Filesystem;

class DirectoryStructureCreator
{

    /**
     * @var \Shopsys\ShopBundle\Component\Image\Config\ImageConfig
     */
    private $imageConfig;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\ImageLocator
     */
    private $imageLocator;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesysytem;

    /**
     * @var string
     */
    private $imageDir;

    /**
     * @var string
     */
    private $domainImageDir;

    /**
     * @param string $imageDir
     * @param string $domainImageDir
     * @param \Shopsys\ShopBundle\Component\Image\Config\ImageConfig $imageConfig
     * @param \Shopsys\ShopBundle\Component\Image\ImageLocator $imageLocator
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(
        $imageDir,
        $domainImageDir,
        ImageConfig $imageConfig,
        ImageLocator $imageLocator,
        Filesystem $filesystem
    ) {
        $this->imageDir = $imageDir;
        $this->domainImageDir = $domainImageDir;
        $this->imageConfig = $imageConfig;
        $this->imageLocator = $imageLocator;
        $this->filesysytem = $filesystem;
    }

    public function makeImageDirectories() {
        $imageEntityConfigs = $this->imageConfig->getAllImageEntityConfigsByClass();
        $directories = [];
        foreach ($imageEntityConfigs as $imageEntityConfig) {
            $sizeConfigs = $imageEntityConfig->getSizeConfigs();
            $sizesDirectories = $this->getTargetDirectoriesFromSizeConfigs(
                $imageEntityConfig->getEntityName(),
                null,
                $sizeConfigs
            );
            $directories = array_merge($directories, $sizesDirectories);

            foreach ($imageEntityConfig->getTypes() as $type) {
                $typeSizes = $imageEntityConfig->getSizeConfigsByType($type);
                $typeSizesDirectories = $this->getTargetDirectoriesFromSizeConfigs(
                    $imageEntityConfig->getEntityName(),
                    $type,
                    $typeSizes
                );
                $directories = array_merge($directories, $typeSizesDirectories);
            }
        }

        $directories[] = $this->domainImageDir;

        $this->filesysytem->mkdir($directories);
    }

    /**
     * @param string $entityName
     * @param string|null $type
     * @param \Shopsys\ShopBundle\Component\Image\Config\ImageSizeConfig[] $sizeConfigs
     * @return string[]
     */
    private function getTargetDirectoriesFromSizeConfigs($entityName, $type, array $sizeConfigs) {
        $directories = [];
        foreach ($sizeConfigs as $sizeConfig) {
            $relativePath = $this->imageLocator->getRelativeImagePath($entityName, $type, $sizeConfig->getName());
            $directories[] = $this->imageDir . $relativePath;
        }

        return $directories;
    }
}
