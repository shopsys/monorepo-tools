<?php

namespace Shopsys\ShopBundle\Component\Image\Config;

use Shopsys\ShopBundle\Component\Utils;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;

class ImageConfigLoader
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\Config\ImageEntityConfig[]
     */
    private $foundEntityConfigs;

    /**
     * @var array
     */
    private $foundEntityNames;

    /**
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $filename
     * @return \Shopsys\ShopBundle\Component\Image\Config\ImageConfig
     */
    public function loadFromYaml($filename)
    {
        $yamlParser = new Parser();

        if (!$this->filesystem->exists($filename)) {
            throw new \Symfony\Component\Filesystem\Exception\FileNotFoundException(
                'File ' . $filename . ' does not exist'
            );
        }

        $imageConfigDefinition = new ImageConfigDefinition();
        $processor = new Processor();

        $inputConfig = $yamlParser->parse(file_get_contents($filename));
        $outputConfig = $processor->processConfiguration($imageConfigDefinition, [$inputConfig]);

        $preparedConfig = $this->loadFromArray($outputConfig);

        return new ImageConfig($preparedConfig);
    }

    /**
     * @param array $outputConfig
     * @return \Shopsys\ShopBundle\Component\Image\Config\ImageEntityConfig[]
     */
    public function loadFromArray($outputConfig)
    {
        $this->foundEntityConfigs = [];
        $this->foundEntityNames = [];

        foreach ($outputConfig as $entityConfig) {
            try {
                $this->processEntityConfig($entityConfig);
            } catch (\Shopsys\ShopBundle\Component\Image\Config\Exception\ImageConfigException $e) {
                throw new \Shopsys\ShopBundle\Component\Image\Config\Exception\EntityParseException(
                    $entityConfig[ImageConfigDefinition::CONFIG_CLASS],
                    $e
                );
            }
        }

        return $this->foundEntityConfigs;
    }

    /**
     * @param array $entityConfig
     */
    private function processEntityConfig($entityConfig)
    {
        $entityClass = $entityConfig[ImageConfigDefinition::CONFIG_CLASS];
        $entityName = $entityConfig[ImageConfigDefinition::CONFIG_ENTITY_NAME];

        if (array_key_exists($entityClass, $this->foundEntityConfigs)
            || array_key_exists($entityName, $this->foundEntityNames)
        ) {
            throw new \Shopsys\ShopBundle\Component\Image\Config\Exception\DuplicateEntityNameException($entityName);
        }

        $types = $this->prepareTypes($entityConfig[ImageConfigDefinition::CONFIG_TYPES]);
        $sizes = $this->prepareSizes($entityConfig[ImageConfigDefinition::CONFIG_SIZES]);
        $multipleByType = $this->getMultipleByType($entityConfig);

        $imageEntityConfig = new ImageEntityConfig($entityName, $entityClass, $types, $sizes, $multipleByType);
        $this->foundEntityNames[$entityName] = $entityName;
        $this->foundEntityConfigs[$entityClass] = $imageEntityConfig;
    }

    /**
     * @param array $sizesConfig
     * @return \Shopsys\ShopBundle\Component\Image\Config\ImageSizeConfig[]
     */
    private function prepareSizes($sizesConfig)
    {
        $result = [];
        foreach ($sizesConfig as $sizeConfig) {
            $sizeName = $sizeConfig[ImageConfigDefinition::CONFIG_SIZE_NAME];
            $key = Utils::ifNull($sizeName, ImageEntityConfig::WITHOUT_NAME_KEY);
            if (!array_key_exists($key, $result)) {
                $result[$key] = new ImageSizeConfig(
                    $sizeName,
                    $sizeConfig[ImageConfigDefinition::CONFIG_SIZE_WIDTH],
                    $sizeConfig[ImageConfigDefinition::CONFIG_SIZE_HEIGHT],
                    $sizeConfig[ImageConfigDefinition::CONFIG_SIZE_CROP],
                    $sizeConfig[ImageConfigDefinition::CONFIG_SIZE_OCCURRENCE]
                );
            } else {
                throw new \Shopsys\ShopBundle\Component\Image\Config\Exception\DuplicateSizeNameException($sizeName);
            }
        }
        if (!array_key_exists(ImageConfig::ORIGINAL_SIZE_NAME, $result)) {
            $result[ImageConfig::ORIGINAL_SIZE_NAME] = new ImageSizeConfig(ImageConfig::ORIGINAL_SIZE_NAME, null, null, false, null);
        }

        return $result;
    }

    /**
     * @param array $typesConfig
     * @return array
     */
    private function prepareTypes($typesConfig)
    {
        $result = [];
        foreach ($typesConfig as $typeConfig) {
            $typeName = $typeConfig[ImageConfigDefinition::CONFIG_TYPE_NAME];
            if (!array_key_exists($typeName, $result)) {
                $result[$typeName] = $this->prepareSizes($typeConfig[ImageConfigDefinition::CONFIG_SIZES]);
            } else {
                throw new \Shopsys\ShopBundle\Component\Image\Config\Exception\DuplicateTypeNameException($typeName);
            }
        }

        return $result;
    }

    /**
     * @param array $entityConfig
     * @return array
     */
    private function getMultipleByType(array $entityConfig)
    {
        $multipleByType = [];
        $multipleByType[ImageEntityConfig::WITHOUT_NAME_KEY] = $entityConfig[ImageConfigDefinition::CONFIG_MULTIPLE];
        foreach ($entityConfig[ImageConfigDefinition::CONFIG_TYPES] as $typeConfig) {
            $type = $typeConfig[ImageConfigDefinition::CONFIG_TYPE_NAME];
            $multiple = $typeConfig[ImageConfigDefinition::CONFIG_MULTIPLE];
            $multipleByType[$type] = $multiple;
        }

        return $multipleByType;
    }
}
