<?php

namespace Shopsys\ShopBundle\Twig;

use Shopsys\ShopBundle\Component\FileUpload\FileUpload;
use Twig_Extension;
use Twig_SimpleFunction;

class FileUploadExtension extends Twig_Extension
{
    /**
     * @var \Shopsys\ShopBundle\Component\FileUpload\FileUpload
     */
    private $fileUpload;

    /**
     * @param \Shopsys\ShopBundle\Component\FileUpload\FileUpload $fileUpload
     */
    public function __construct(FileUpload $fileUpload)
    {
        $this->fileUpload = $fileUpload;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('getLabelByTemporaryFilename', [$this, 'getLabelByTemporaryFilename']),
        ];
    }

    /**
     * @param string $temporaryFilename
     * @return string
     */
    public function getLabelByTemporaryFilename($temporaryFilename)
    {
        $filename = $this->fileUpload->getOriginalFilenameByTemporary($temporaryFilename);
        $filepath = ($this->fileUpload->getTemporaryDirectory() . '/' . $temporaryFilename);
        if (file_exists($filepath) && is_file($filepath) && is_writable($filepath)) {
            $fileSize = round((int)filesize($filepath) / 1000 / 1000, 2); //https://en.wikipedia.org/wiki/Binary_prefix
            return $filename . ' (' . $fileSize . ' MB)';
        }
        return '';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fileupload_extension';
    }
}
