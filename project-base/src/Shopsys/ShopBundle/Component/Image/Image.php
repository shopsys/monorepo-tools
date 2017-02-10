<?php

namespace Shopsys\ShopBundle\Component\Image;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Shopsys\ShopBundle\Component\FileUpload\EntityFileUploadInterface;
use Shopsys\ShopBundle\Component\FileUpload\FileForUpload;
use Shopsys\ShopBundle\Component\FileUpload\FileNamingConvention;
use Shopsys\ShopBundle\Component\Image\Config\ImageConfig;

/**
 * @ORM\Table(name="images", indexes={@ORM\Index(columns={"entity_name", "entity_id", "type"})})
 * @ORM\Entity
 */
class Image implements EntityFileUploadInterface
{

    const UPLOAD_KEY = 'image';

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
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=5)
     */
    private $extension;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

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
     * @param string|null $type
     * @param string $temporaryFilename
     */
    public function __construct($entityName, $entityId, $type, $temporaryFilename) {
        $this->entityName = $entityName;
        $this->entityId = $entityId;
        $this->type = $type;
        $this->setTemporaryFilename($temporaryFilename);
    }

    /**
     * @return \Shopsys\ShopBundle\Component\FileUpload\FileForUpload[]
     */
    public function getTemporaryFilesForUpload() {
        $files = [];
        if ($this->temporaryFilename !== null) {
            $files[self::UPLOAD_KEY] = new FileForUpload(
                $this->temporaryFilename,
                true,
                $this->entityName,
                $this->type . '/' . ImageConfig::ORIGINAL_SIZE_NAME,
                FileNamingConvention::TYPE_ID
            );
        }
        return $files;
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
     * @param int $position
     */
    public function setPosition($position) {
        $this->position = $position;
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
     * @return string|null
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getExtension() {
        return $this->extension;
    }

    /**
     * @return \DateTime
     */
    public function getModifiedAt() {
        return $this->modifiedAt;
    }

}
