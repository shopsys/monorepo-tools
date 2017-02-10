<?php

namespace Shopsys\AutoServicesBundle\Compiler;

use Symfony\Component\Filesystem\Filesystem;

class AutoServicesCollector
{
    const CONFIG_FILENAME = 'autoServices.json';

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var string
     */
    private $containerClass;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var string[]|null
     */
    private $classesByServiceId;

    public function __construct($cacheDir, $containerClass, Filesystem $filesystem)
    {
        $this->cacheDir = $cacheDir;
        $this->containerClass = $containerClass;
        $this->filesystem = $filesystem;
    }

    /**
     * @return string
     */
    private function getConfigFilepath()
    {
        return $this->cacheDir . '/' . self::CONFIG_FILENAME;
    }

    /**
     * @return string[][]
     */
    public function getServicesClassesIndexedByServiceId()
    {
        $this->load();
        return $this->classesByServiceId;
    }

    /**
     * @param string $serviceId
     * @param string $className
     */
    public function addService($serviceId, $className)
    {
        $this->load();
        $this->classesByServiceId[$serviceId] = $className;
        $this->flush();
        $this->invalidateContainer();
    }

    /**
     * @param string[] $classesByServiceId
     */
    public function setServices(array $classesByServiceId)
    {
        $this->classesByServiceId = $classesByServiceId;
        $this->flush();
    }

    private function invalidateContainer()
    {
        $containerClassFilepath = $this->cacheDir . '/' . $this->containerClass . '.php';
        $this->filesystem->remove($containerClassFilepath);
    }

    private function flush()
    {
        $jsonConfig = json_encode($this->classesByServiceId, JSON_PRETTY_PRINT);
        file_put_contents($this->getConfigFilepath(), $jsonConfig);
    }

    private function load()
    {
        if ($this->classesByServiceId === null) {
            if (file_exists($this->getConfigFilepath())) {
                $jsonConfig = file_get_contents($this->getConfigFilepath());
                $this->classesByServiceId = json_decode($jsonConfig, true);
            }

            if (!is_array($this->classesByServiceId)) {
                $this->classesByServiceId = [];
            }
        }
    }
}
