<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;


/**
 * @ORM\Table(options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Repository\GuideRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogGuide")
 * @ApiResource()
 * @ApiFilter(SearchFilter::class, properties={"phone":"exact"})
 */
class Guide
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $nameSurname;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GuideGroup", inversedBy="guides")
     * @Gedmo\Versioned()
     */
    private $guideGroupID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $title;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned()
     */
    private $targetType;

    /**
     * @ORM\Column(type="bigint")
     * @Gedmo\Versioned()
     */
    private $targetId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getNameSurname(): ?string
    {
        return $this->nameSurname;
    }

    public function setNameSurname(string $nameSurname): self
    {
        $this->nameSurname = $nameSurname;

        return $this;
    }

    public function getGuideGroupID(): ?GuideGroup
    {
        return $this->guideGroupID;
    }

    public function setGuideGroupID(?GuideGroup $guideGroupID): self
    {
        $this->guideGroupID = $guideGroupID;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTargetType(): ?int
    {
        return $this->targetType;
    }

    public function setTargetType(int $targetType): self
    {
        $this->targetType = $targetType;

        return $this;
    }

    public function getTargetId(): ?int
    {
        return $this->targetId;
    }

    public function setTargetId(int $targetId): self
    {
        $this->targetId = $targetId;

        return $this;
    }
}
