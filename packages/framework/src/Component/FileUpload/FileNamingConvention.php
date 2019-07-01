<?php

namespace Shopsys\FrameworkBundle\Component\FileUpload;

class FileNamingConvention
{
    public const TYPE_ID = 1;
    protected const TYPE_ORIGINAL_NAME = 2;

    /**
     * @param int $namingConventionType
     * @param string $originalFilename
     * @param int|null $entityId
     * @return string
     */
    public function getFilenameByNamingConvention($namingConventionType, $originalFilename, $entityId = null)
    {
        if ($namingConventionType === self::TYPE_ID && is_int($entityId)) {
            return $entityId . '.' . pathinfo($originalFilename, PATHINFO_EXTENSION);
        } elseif ($namingConventionType === static::TYPE_ORIGINAL_NAME) {
            return $originalFilename;
        } else {
            $message = 'Naming convention ' . $namingConventionType . ' cannot by resolved to filename';
            throw new \Shopsys\FrameworkBundle\Component\FileUpload\Exception\UnresolvedNamingConventionException($message);
        }
    }
}
