<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Targets
 * @ApiResource()
 * @ORM\Table(name="targets",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\TargetsRepository")
 * @Gedmo\Loggable()
 */
class Targets
{
    /**
     * @var int
     *
     * @ORM\Column(name="target_type", type="integer", nullable=false)
     * @ORM\Id
     */
    private $targetType;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="img_src", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $imgSrc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="table_name", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $tableName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="target_name", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $targetName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="target_value", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $targetValue;

    /**
     * @var string|null
     *
     * @ORM\Column(name="target_str", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $targetStr;

    public function getTargetType(): ?int
    {
        return $this->targetType;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImgSrc(): ?string
    {
        return $this->imgSrc;
    }

    public function setImgSrc(?string $imgSrc): self
    {
        $this->imgSrc = $imgSrc;

        return $this;
    }

    public function getTableName(): ?string
    {
        return $this->tableName;
    }

    public function setTableName(?string $tableName): self
    {
        $this->tableName = $tableName;

        return $this;
    }

    public function getTargetName(): ?string
    {
        return $this->targetName;
    }

    public function setTargetName(?string $targetName): self
    {
        $this->targetName = $targetName;

        return $this;
    }

    public function getTargetValue(): ?string
    {
        return $this->targetValue;
    }

    public function setTargetValue(?string $targetValue): self
    {
        $this->targetValue = $targetValue;

        return $this;
    }

    public function getTargetStr(): ?string
    {
        return $this->targetStr;
    }

    public function setTargetStr(?string $targetStr): self
    {
        $this->targetStr = $targetStr;

        return $this;
    }


}
