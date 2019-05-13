<?php

declare(strict_types=1);

namespace Shopsys\ReadModelBundle\Image;

/**
 * @experimental
 *
 * Class representing images in frontend
 *
 * @see https://github.com/shopsys/shopsys/blob/master/docs/model/introduction-to-read-model.md
 */
class ImageView
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @var string|null
     */
    protected $type;

    /**
     * @param int $id
     * @param string $extension
     * @param string $entityName
     * @param string|null $type
     */
    public function __construct(int $id, string $extension, string $entityName, ?string $type)
    {
        $this->id = $id;
        $this->extension = $extension;
        $this->entityName = $entityName;
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return $this->entityName;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }
}
