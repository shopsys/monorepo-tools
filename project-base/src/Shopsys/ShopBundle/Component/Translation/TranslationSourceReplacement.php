<?php

namespace Shopsys\ShopBundle\Component\Translation;

class TranslationSourceReplacement
{
    /**
     * @var string
     */
    private $oldSource;

    /**
     * @var string
     */
    private $newSource;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var string[]
     */
    private $sourceFileReferences;

    /**
     * @param string $oldSource
     * @param string $newSource
     * @param string $domain
     * @param string[] $sourceFileReferences
     */
    public function __construct($oldSource, $newSource, $domain, array $sourceFileReferences) {
        $this->oldSource = $oldSource;
        $this->newSource = $newSource;
        $this->domain = $domain;
        $this->sourceFileReferences = $sourceFileReferences;
    }

    /**
     * @return string
     */
    public function getOldSource() {
        return $this->oldSource;
    }

    /**
     * @return string
     */
    public function getNewSource() {
        return $this->newSource;
    }

    /**
     * @return string
     */
    public function getDomain() {
        return $this->domain;
    }

    /**
     * Paths relative to any of directories that are scanned for translations
     * @return string[]
     */
    public function getSourceFilePaths() {
        $sourceFilePaths = [];
        foreach ($this->sourceFileReferences as $sourceFileReference) {
            $sourceFilePaths[] = $this->extractSourceFilePathFromReference($sourceFileReference);
        }

        return array_unique($sourceFilePaths);
    }

    /**
     * @param string $sourceFilePath
     * @return int
     */
    public function getExpectedReplacementsCountForSourceFilePath($sourceFilePath) {
        $expectedReplacementsCount = 0;
        foreach ($this->sourceFileReferences as $sourceFileReference) {
            if ($this->extractSourceFilePathFromReference($sourceFileReference) === $sourceFilePath) {
                $expectedReplacementsCount++;
            }
        }

        return $expectedReplacementsCount;
    }

    /**
     * @param string $sourceFilePath
     * @return bool
     */
    public function isExpectedReplacementsCountExact($sourceFilePath) {
        foreach ($this->sourceFileReferences as $sourceFileReference) {
            if ($this->extractSourceFilePathFromReference($sourceFileReference) === $sourceFilePath) {
                if ($this->extractSourceFileLineFromReference($sourceFileReference) === null) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param string $sourceFileReference
     * @return string
     */
    private function extractSourceFilePathFromReference($sourceFileReference) {
        return explode(':', $sourceFileReference)[0];
    }

    /**
     * @param string $sourceFileReference
     * @return int|null
     */
    private function extractSourceFileLineFromReference($sourceFileReference) {
        $parts = explode(':', $sourceFileReference);

        return count($parts) > 1 && is_numeric($parts[1]) ? (int)$parts[1] : null;
    }
}
