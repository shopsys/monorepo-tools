<?php

namespace Shopsys\ShopBundle\Component\UploadedFile;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\UploadedFile\UploadedFile;

class UploadedFileRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getUploadedFileRepository()
    {
        return $this->em->getRepository(UploadedFile::class);
    }

    /**
     * @param string $entityName
     * @param int $entityId
     * @return \Shopsys\ShopBundle\Component\UploadedFile\UploadedFile|null
     */
    public function findUploadedFileByEntity($entityName, $entityId)
    {
        return $this->getUploadedFileRepository()->findOneBy([
            'entityName' => $entityName,
            'entityId' => $entityId,
        ]);
    }

    /**
     * @param string $entityName
     * @param int $entityId
     * @return \Shopsys\ShopBundle\Component\UploadedFile\UploadedFile
     */
    public function getUploadedFileByEntity($entityName, $entityId)
    {
        $uploadedFile = $this->findUploadedFileByEntity($entityName, $entityId);
        if ($uploadedFile === null) {
            $message = 'UploadedFile not found for entity "' . $entityName . '" with ID ' . $entityId;
            throw new \Shopsys\ShopBundle\Component\UploadedFile\Exception\FileNotFoundException($message);
        }

        return $uploadedFile;
    }

    /**
     * @param int $uploadedFileId
     * @return \Shopsys\ShopBundle\Component\UploadedFile\UploadedFile
     */
    public function getById($uploadedFileId)
    {
        $uploadedFile = $this->getUploadedFileRepository()->find($uploadedFileId);

        if ($uploadedFile === null) {
            $message = 'UploadedFile with ID ' . $uploadedFileId . ' does not exist.';
            throw new \Shopsys\ShopBundle\Component\UploadedFile\Exception\FileNotFoundException($message);
        }

        return $uploadedFile;
    }
}
