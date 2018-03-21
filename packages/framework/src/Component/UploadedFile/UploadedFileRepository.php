<?php

namespace Shopsys\FrameworkBundle\Component\UploadedFile;

use Doctrine\ORM\EntityManagerInterface;

class UploadedFileRepository
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
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
     * @return \Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFile|null
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
     * @return \Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFile
     */
    public function getUploadedFileByEntity($entityName, $entityId)
    {
        $uploadedFile = $this->findUploadedFileByEntity($entityName, $entityId);
        if ($uploadedFile === null) {
            $message = 'UploadedFile not found for entity "' . $entityName . '" with ID ' . $entityId;
            throw new \Shopsys\FrameworkBundle\Component\UploadedFile\Exception\FileNotFoundException($message);
        }

        return $uploadedFile;
    }

    /**
     * @param int $uploadedFileId
     * @return \Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFile
     */
    public function getById($uploadedFileId)
    {
        $uploadedFile = $this->getUploadedFileRepository()->find($uploadedFileId);

        if ($uploadedFile === null) {
            $message = 'UploadedFile with ID ' . $uploadedFileId . ' does not exist.';
            throw new \Shopsys\FrameworkBundle\Component\UploadedFile\Exception\FileNotFoundException($message);
        }

        return $uploadedFile;
    }
}
