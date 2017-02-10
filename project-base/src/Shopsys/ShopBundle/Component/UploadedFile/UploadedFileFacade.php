<?php

namespace Shopsys\ShopBundle\Component\UploadedFile;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\UploadedFile\Config\UploadedFileConfig;
use Shopsys\ShopBundle\Component\UploadedFile\UploadedFile;
use Shopsys\ShopBundle\Component\UploadedFile\UploadedFileLocator;
use Shopsys\ShopBundle\Component\UploadedFile\UploadedFileRepository;
use Shopsys\ShopBundle\Component\UploadedFile\UploadedFileService;
use Symfony\Component\Filesystem\Filesystem;

class UploadedFileFacade {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Component\UploadedFile\Config\UploadedFileConfig
     */
    private $uploadedFileConfig;

    /**
     * @var \Shopsys\ShopBundle\Component\UploadedFile\UploadedFileRepository
     */
    private $uploadedFileRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\UploadedFile\UploadedFileService
     */
    private $uploadedFileService;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \Shopsys\ShopBundle\Component\UploadedFile\UploadedFileLocator
     */
    private $uploadedFileLocator;

    public function __construct(
        EntityManager $em,
        UploadedFileConfig $uploadedFileConfig,
        UploadedFileRepository $uploadedFileRepository,
        UploadedFileService $uploadedFileService,
        Filesystem $filesystem,
        UploadedFileLocator $uploadedFileLocator
    ) {
        $this->em = $em;
        $this->uploadedFileConfig = $uploadedFileConfig;
        $this->uploadedFileRepository = $uploadedFileRepository;
        $this->uploadedFileService = $uploadedFileService;
        $this->filesystem = $filesystem;
        $this->uploadedFileLocator = $uploadedFileLocator;
    }

    /**
     * @param object $entity
     * @param array|null $temporaryFilenames
     */
    public function uploadFile($entity, $temporaryFilenames) {
        if ($temporaryFilenames !== null && count($temporaryFilenames) > 0) {
            $entitiesForFlush = [];
            $uploadedFileEntityConfig = $this->uploadedFileConfig->getUploadedFileEntityConfig($entity);
            $entityId = $this->getEntityId($entity);
            $oldUploadedFile = $this->uploadedFileRepository->findUploadedFileByEntity(
                $uploadedFileEntityConfig->getEntityName(),
                $entityId
            );

            if ($oldUploadedFile !== null) {
                $this->em->remove($oldUploadedFile);
                $entitiesForFlush[] = $oldUploadedFile;
            }

            $newUploadedFile = $this->uploadedFileService->createUploadedFile(
                $uploadedFileEntityConfig,
                $entityId,
                $temporaryFilenames
            );
            $this->em->persist($newUploadedFile);
            $entitiesForFlush[] = $newUploadedFile;

            $this->em->flush($entitiesForFlush);
        }
    }

    /**
     * @param object $entity
     */
    public function deleteUploadedFileByEntity($entity) {
        $uploadedFile = $this->getUploadedFileByEntity($entity);
        $this->em->remove($uploadedFile);
        $this->em->flush();
    }

    /**
     * @param \Shopsys\ShopBundle\Component\UploadedFile\UploadedFile $uploadedFile
     */
    public function deleteFileFromFilesystem(UploadedFile $uploadedFile) {
        $filepath = $this->uploadedFileLocator->getAbsoluteUploadedFileFilepath($uploadedFile);
        $this->filesystem->remove($filepath);
    }

    /**
     * @param object $entity
     * @return \Shopsys\ShopBundle\Component\UploadedFile\UploadedFile
     */
    public function getUploadedFileByEntity($entity) {
        return $this->uploadedFileRepository->getUploadedFileByEntity(
            $this->uploadedFileConfig->getEntityName($entity),
            $this->getEntityId($entity)
        );
    }

    /**
     * @param object $entity
     * @return int
     */
    private function getEntityId($entity) {
        $entityMetadata = $this->em->getClassMetadata(get_class($entity));
        $identifier = $entityMetadata->getIdentifierValues($entity);
        if (count($identifier) === 1) {
            return array_pop($identifier);
        }

        $message = 'Entity "' . get_class($entity) . '" has not set primary key or primary key is compound."';
        throw new \Shopsys\ShopBundle\Component\UploadedFile\Exception\EntityIdentifierException($message);
    }

    /**
     * @param int $uploadedFileId
     * @return \Shopsys\ShopBundle\Component\UploadedFile\UploadedFile
     */
    public function getById($uploadedFileId) {
        return $this->uploadedFileRepository->getById($uploadedFileId);
    }

    /**
     * @param Object $entity
     * @return bool
     */
    public function hasUploadedFile($entity) {
        try {
            $uploadedFile = $this->getUploadedFileByEntity($entity);
        } catch (\Shopsys\ShopBundle\Component\UploadedFile\Exception\FileNotFoundException $e) {
            return false;
        }

        return $this->uploadedFileLocator->fileExists($uploadedFile);
    }

    /**
     * @param \Shopsys\ShopBundle\Component\UploadedFile\UploadedFile $uploadedFile
     * @return string
     */
    public function getAbsoluteUploadedFileFilepath(UploadedFile $uploadedFile) {
        return $this->uploadedFileLocator->getAbsoluteUploadedFileFilepath($uploadedFile);
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Shopsys\ShopBundle\Component\UploadedFile\UploadedFile $uploadedFile
     * @return string
     */
    public function getUploadedFileUrl(DomainConfig $domainConfig, UploadedFile $uploadedFile) {
        return $this->uploadedFileLocator->getUploadedFileUrl($domainConfig, $uploadedFile);
    }

}
