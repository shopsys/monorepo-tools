<?php

namespace Shopsys\ShopBundle\Component\Filesystem;

class FilepathComparator
{

    /**
     * @param string $path
     * @param string $directoryPath
     * @return bool
     */
    public function isPathWithinDirectory($path, $directoryPath) {
        $directoryPathRealpath = realpath($directoryPath);
        if ($directoryPathRealpath === false) {
            throw new \Shopsys\ShopBundle\Component\Filesystem\Exception\DirectoryDoesNotExistException(
                $directoryPath
            );
        }

        return $this->isPathWithinDirectoryRealpathRecursive($path, $directoryPathRealpath);
    }

    /**
     * @param string $path
     * @param string $directoryRealpath
     * @return bool
     */
    private function isPathWithinDirectoryRealpathRecursive($path, $directoryRealpath) {
        if (realpath($path) === $directoryRealpath) {
            return true;
        }

        if ($this->hasAncestorPath($path)) {
            return $this->isPathWithinDirectoryRealpathRecursive(dirname($path), $directoryRealpath);
        } else {
            return false;
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    private function hasAncestorPath($path) {
        return dirname($path) !== $path;
    }

}
