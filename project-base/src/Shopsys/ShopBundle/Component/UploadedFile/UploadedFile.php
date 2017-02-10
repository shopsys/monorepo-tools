<?php

namespace Shopsys\ShopBundle\Component\UploadedFile;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Shopsys\ShopBundle\Component\FileUpload\EntityFileUploadInterface;
use Shopsys\ShopBundle\Component\FileUpload\FileForUpload;
use Shopsys\ShopBundle\Component\FileUpload\FileNamingConvention;

/**
 * @ORM\Table(name="uploaded_files", indexes={@ORM\Index(columns={"entity_name", "entity_id"})})
 * @ORM\Entity
 */
class UploadedFile implements EntityFileUploadInterface {

    const UPLOAD_KEY = 'uploadedFile';

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    private $entityName;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $entityId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=5)
     */
    private $extension;

    /**
     * @var \Datetime
     *
     * @ORM\Column(type="datetime")
     */
    private $modifiedAt;

    /**
     * @var string|null
     */
    private $temporaryFilename;

    /**
     * @param string $entityName
     * @param int $entityId
     * @param string $temporaryFilename
     */
    public function __construct($entityName, $entityId, $temporaryFilename) {
        $this->entityName = $entityName;
        $this->entityId = $entityId;
        $this->setTemporaryFilename($temporaryFilename);
    }

    /**
     * @return \Shopsys\ShopBundle\Component\FileUpload\FileForUpload[]
     */
    public function getTemporaryFilesForUpload() {
        if ($this->temporaryFilename === null) {
            return [];
        }

        return [
            self::UPLOAD_KEY => new FileForUpload(
                $this->temporaryFilename,
                false,
                $this->entityName,
                null,
                FileNamingConvention::TYPE_ID
            ),
        ];
    }

    /**
     * @param string $key
     * @param string $originalFilename
     */
    public function setFileAsUploaded($key, $originalFilename) {
        if ($key === self::UPLOAD_KEY) {
            $this->extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        } else {
            throw new \Shopsys\ShopBundle\Component\FileUpload\Exception\InvalidFileKeyException($key);
        }
    }

    /**
     * @param string|null $temporaryFilename
     */
    public function setTemporaryFilename($temporaryFilename) {
        $this->temporaryFilename = $temporaryFilename;
        // workaround: Entity must be changed so that preUpdate and postUpdate are called
        $this->modifiedAt = new DateTime();
    }

    /**
     * @return string
     */
    public function getFilename() {
        return $this->id . '.' . $this->extension;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEntityName() {
        return $this->entityName;
    }

    /**
     * @return int
     */
    public function getEntityId() {
        return $this->entityId;
    }

    /**
     * @return string
     */
    public function getExtension() {
        return $this->extension;
    }

}
