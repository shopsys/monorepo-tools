<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\Filesystem;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Filesystem\Exception\DirectoryDoesNotExistException;
use Shopsys\ShopBundle\Component\Filesystem\FilepathComparator;

class FilepathComparatorTest extends PHPUnit_Framework_TestCase {

    public function testIsPathWithinDirectoryThrowsExceptionWithNonExistentDirectoryPath() {
        $filepathComparator = new FilepathComparator();

        $path = 'anyPath';
        $nonExistentPath = 'nonExistentPath';

        $this->setExpectedException(DirectoryDoesNotExistException::class);
        $filepathComparator->isPathWithinDirectory($path, $nonExistentPath);
    }

    public function testIsPathWithinAnotherExistingPathReturnsTrueForFileInsideDirectory() {
        $filepathComparator = new FilepathComparator();

        $path = $this->getResourcePath('dir/fileInside');
        $directoryPath = $this->getResourcePath('dir');

        $this->assertTrue($filepathComparator->isPathWithinDirectory($path, $directoryPath));
    }

    public function testIsPathWithinAnotherExistingPathReturnsFalseForFileOutsideDirectory() {
        $filepathComparator = new FilepathComparator();

        $path = $this->getResourcePath('fileOutside');
        $directoryPath = $this->getResourcePath('dir');

        $this->assertFalse($filepathComparator->isPathWithinDirectory($path, $directoryPath));
    }

    public function testIsPathWithinAnotherExistingPathReturnsTrueForDirectorySelf() {
        $filepathComparator = new FilepathComparator();

        $path = $this->getResourcePath('dir');
        $directoryPath = $this->getResourcePath('dir');

        $this->assertTrue($filepathComparator->isPathWithinDirectory($path, $directoryPath));
    }

    public function testIsPathWithinAnotherExistingPathReturnsTrueForNonExistentFileInsideDirectory() {
        $filepathComparator = new FilepathComparator();

        $path = $this->getResourcePath('dir/nonexistentFileInside');
        $directoryPath = $this->getResourcePath('dir');

        $this->assertTrue($filepathComparator->isPathWithinDirectory($path, $directoryPath));
    }

    /**
     * @param string $relativePath
     * @return string
     */
    private function getResourcePath($relativePath) {
        return __DIR__ . '/Resources/' . $relativePath;
    }

}
