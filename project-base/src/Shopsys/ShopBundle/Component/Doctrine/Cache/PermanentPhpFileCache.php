<?php

namespace Shopsys\ShopBundle\Component\Doctrine\Cache;

use Doctrine\Common\Cache\PhpFileCache;

class PermanentPhpFileCache extends PhpFileCache
{

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id) {
        $fileName = $this->getFilename($id);

        // note: error suppression is still faster than `file_exists`, `is_file` and `is_readable`
        // @codingStandardsIgnoreStart
        $value = @include $fileName;
        // @codingStandardsIgnoreEnd

        return unserialize($value);
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id) {
        $value = $this->doFetch($id);

        return $value !== false;
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0) {
        if ($lifeTime !== 0 && $lifeTime !== null) {
            $message = self::class . ' does not support $lifetime';
            throw new \Shopsys\ShopBundle\Component\Doctrine\Cache\Exception\InvalidArgumentException($message);
        }

        $filename = $this->getFilename($id);

        $code = '<?php return ' . var_export(serialize($data), true) . ';';

        return $this->writeFile($filename, $code);
    }

}
