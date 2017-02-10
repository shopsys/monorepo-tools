<?php

namespace Shopsys\ShopBundle\Twig\Javascript;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Javascript\Compiler\JsCompiler;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class JavascriptCompilerService
{

    const NOT_COMPILED_FOLDER = '/plugins/';

    /**
     * @var string
     */
    private $rootPath;

    /**
     * @var string
     */
    private $webPath;

    /**
     * @var string
     */
    private $jsUrlPrefix;

    /**
     * @var string
     */
    private $jsSourcePath;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Component\Javascript\Compiler\JsCompiler
     */
    private $jsCompiler;

    /**
     * @var array
     */
    private $javascriptLinks = [];

    /**
     * @var \Symfony\Component\Asset\Packages
     */
    private $assetPackages;

    public function __construct(
        $rootPath,
        $webPath,
        $jsSourcePath,
        $jsUrlPrefix,
        ContainerInterface $container,
        Filesystem $filesystem,
        Domain $domain,
        JsCompiler $jsCompiler,
        Packages $assetPackages
    ) {
        $this->rootPath = $rootPath;
        $this->webPath = $webPath;
        $this->jsSourcePath = $jsSourcePath;
        $this->jsUrlPrefix = $jsUrlPrefix;
        $this->container = $container;
        $this->filesystem = $filesystem;
        $this->domain = $domain;
        $this->jsCompiler = $jsCompiler;
        $this->assetPackages = $assetPackages;
    }

    /**
     * @param string[] $javascripts
     * @return string[] URLs of compiled JS files
     */
    public function generateCompiledFiles(array $javascripts) {
        $this->javascriptLinks = [];

        foreach ($javascripts as $javascript) {
            $this->process($javascript);
        }

        return array_unique($this->javascriptLinks);
    }

    /**
     * @param string $javascript
     */
    private function process($javascript) {
        if ($this->tryToProcessJavascriptFile($javascript)) {
            return;
        }

        if ($this->tryToProcessJavascriptDirectoryMask($javascript)) {
            return;
        }

        $this->processExternalJavascript($javascript);
    }

    /**
     * @param string $javascript
     * @return bool
     */
    private function tryToProcessJavascriptFile($javascript) {
        $sourcePath = $this->jsSourcePath . '/' . $javascript;
        $relativeTargetPath = $this->getRelativeTargetPath($javascript);

        if ($relativeTargetPath === null) {
            return false;
        }

        if (is_file($sourcePath)) {
            $lastModified = filemtime($sourcePath);
            $relativeTargetPathWithTimestamp = $this->getPathWithTimestamp($relativeTargetPath, $lastModified);
            $this->compileJavascriptFile($sourcePath, $relativeTargetPathWithTimestamp);
            $this->javascriptLinks[] = $this->assetPackages->getUrl($relativeTargetPathWithTimestamp);
            return true;
        }

        return false;
    }

    /**
     * @param string $relativePath
     * @param string $timestamp
     * @return string
     */
    private function getPathWithTimestamp($relativePath, $timestamp) {
        $version = '-v' . $timestamp;

        return substr_replace($relativePath, $version, strrpos($relativePath, '.'), 0);
    }

    /**
     * @param string $javascript
     * @return string
     */
    private function getRelativeTargetPath($javascript) {
        $relavitveTargetPath = null;
        if (strpos($javascript, 'admin/') === 0 || strpos($javascript, 'frontend/') === 0) {
            $relavitveTargetPath = substr($this->jsUrlPrefix, 1) . $javascript;
            if (strpos($relavitveTargetPath, '/') === 0) {
                $relavitveTargetPath = substr($relavitveTargetPath, 1);
            }

            $relavitveTargetPath = str_replace('/scripts/', '/scripts/' . $this->domain->getLocale() . '/', $relavitveTargetPath);
        }

        return $relavitveTargetPath;
    }

    /**
     * @param string $sourceFilename
     * @param string $relativeTargetPath
     */
    private function compileJavascriptFile($sourceFilename, $relativeTargetPath) {
        $compiledFilename = $this->webPath . '/' . $relativeTargetPath;

        if (!$this->isCompiledFileFresh($compiledFilename, $sourceFilename)) {
            $content = file_get_contents($sourceFilename);

            if (strpos($sourceFilename, self::NOT_COMPILED_FOLDER) === false) {
                $newContent = $this->jsCompiler->compile($content);
            } else {
                $newContent = $content;
            }

            $this->filesystem->mkdir(dirname($compiledFilename));
            $this->filesystem->dumpFile($compiledFilename, $newContent);
        }
    }

    /**
     * @param string $compiledFilename
     * @param string $sourceFilename
     * @return bool
     */
    private function isCompiledFileFresh($compiledFilename, $sourceFilename) {
        if (is_file($compiledFilename) && parse_url($sourceFilename, PHP_URL_HOST) === null) {
            $isCompiledFileFresh = filemtime($sourceFilename) < filemtime($compiledFilename);
        } else {
            $isCompiledFileFresh = false;
        }
        return $isCompiledFileFresh;
    }

    /**
     * @param string $directoryMask
     * @return bool
     */
    private function tryToProcessJavascriptDirectoryMask($directoryMask) {
        $parts = explode('/', $directoryMask);
        $mask = array_pop($parts);
        $path = implode('/', $parts);

        if (!$this->isMaskValid($mask)) {
            return false;
        }

        $filenameMask = $mask === '' ? '*' : $mask;

        return $this->processJavascriptByMask($path, $filenameMask);
    }

    /**
     * @param string $path
     * @param string $filenameMask
     * @return bool
     */
    private function processJavascriptByMask($path, $filenameMask) {
        $filesystemPath = $this->jsSourcePath . '/' . $path;

        if (is_dir($filesystemPath)) {
            $filepaths = (array)glob($filesystemPath . '/' . $filenameMask);
            foreach ($filepaths as $filepath) {
                $javascript = str_replace($this->jsSourcePath . '/', '', $filepath);
                $this->tryToProcessJavascriptFile($javascript);
            }
        }

        return true;
    }

    /**
     * @param string $filenameMask
     * @return bool
     */
    private function isMaskValid($filenameMask) {
        return $filenameMask === '' || strpos($filenameMask, '*') !== false;
    }

    /**
     * @param string $javascriptUrl
     */
    private function processExternalJavascript($javascriptUrl) {
        $this->javascriptLinks[] = $this->assetPackages->getUrl($javascriptUrl);
    }

}
