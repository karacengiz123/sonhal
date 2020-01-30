<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Recordings
 * @ApiResource()
 * @ORM\Table(name="recordings",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\RecordingsRepository")
 * @Gedmo\Loggable()
 */
class Recordings
{
    /**
     * @var int
     *
     * @ORM\Column(name="idx", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idx;

    /**
     * @var int
     *
     * @ORM\Column(name="cus_id", type="integer", nullable=false)
     * @Gedmo\Versioned()
     */
    private $cusId = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=50, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $description = '';

    /**
     * @var float
     *
     * @ORM\Column(name="duration", type="float", precision=10, scale=2, nullable=false, options={"default"="0.00"})
     * @Gedmo\Versioned()
     */
    private $duration = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="ext", type="string", length=3, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $ext = '';

    public function getIdx(): ?int
    {
        return $this->idx;
    }

    public function getCusId(): ?int
    {
        return $this->cusId;
    }

    public function setCusId(int $cusId): self
    {
        $this->cusId = $cusId;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDuration(): ?float
    {
        return $this->duration;
    }

    public function setDuration(float $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getExt(): ?string
    {
        return $this->ext;
    }

    public function setExt(string $ext): self
    {
        $this->ext = $ext;

        return $this;
    }


}
