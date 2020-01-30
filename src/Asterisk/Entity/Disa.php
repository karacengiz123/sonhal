<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Disa
 * @ApiResource()
 * @ORM\Table(name="disa",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\DisaRepository")
 * @Gedmo\Loggable()
 */
class Disa
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
     * @var string
     *
     * @ORM\Column(name="cus_id", type="string", length=7, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $cusId = '';

    /**
     * @var int
     *
     * @ORM\Column(name="did", type="integer", nullable=false, options={"comment"="customer did id"})
     * @Gedmo\Versioned()
     */
    private $did = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="diversion", type="boolean", nullable=false, options={"comment"="0: disabled, 1: enabled"})
     * @Gedmo\Versioned()
     */
    private $diversion = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="recording", type="boolean", nullable=false)
     * @Gedmo\Versioned()
     */
    private $recording = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $description;

    public function getIdx(): ?int
    {
        return $this->idx;
    }

    public function getCusId(): ?string
    {
        return $this->cusId;
    }

    public function setCusId(string $cusId): self
    {
        $this->cusId = $cusId;

        return $this;
    }

    public function getDid(): ?int
    {
        return $this->did;
    }

    public function setDid(int $did): self
    {
        $this->did = $did;

        return $this;
    }

    public function getDiversion(): ?bool
    {
        return $this->diversion;
    }

    public function setDiversion(bool $diversion): self
    {
        $this->diversion = $diversion;

        return $this;
    }

    public function getRecording(): ?bool
    {
        return $this->recording;
    }

    public function setRecording(bool $recording): self
    {
        $this->recording = $recording;

        return $this;
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


}
