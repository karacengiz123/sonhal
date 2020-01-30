<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * CustomExtens
 * @ApiResource()
 * @ORM\Table(name="custom_extens",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\CustomExtensRepository")
 * @Gedmo\Loggable()
 */
class CustomExtens
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
     * @var string
     *
     * @ORM\Column(name="scode", type="string", length=4, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $scode = '';

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
     * @var string
     *
     * @ORM\Column(name="dialstring", type="string", length=10, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $dialstring = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="n_target_type", type="boolean", nullable=false, options={"comment"="0: hangup, 1: ivr, 2: queue, 3: exten, 4: tc, 5: announcement"})
     * @Gedmo\Versioned()
     */
    private $nTargetType = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="n_target_id", type="bigint", nullable=false)
     * @Gedmo\Versioned()
     */
    private $nTargetId = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="b_target_type", type="boolean", nullable=false, options={"comment"="0: hangup, 1: ivr, 2: queue, 3: exten, 4: tc, 5: announcement"})
     * @Gedmo\Versioned()
     */
    private $bTargetType = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="b_target_id", type="bigint", nullable=false)
     * @Gedmo\Versioned()
     */
    private $bTargetId = '0';

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

    public function getScode(): ?string
    {
        return $this->scode;
    }

    public function setScode(string $scode): self
    {
        $this->scode = $scode;

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

    public function getDialstring(): ?string
    {
        return $this->dialstring;
    }

    public function setDialstring(string $dialstring): self
    {
        $this->dialstring = $dialstring;

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

    public function getNTargetType(): ?bool
    {
        return $this->nTargetType;
    }

    public function setNTargetType(bool $nTargetType): self
    {
        $this->nTargetType = $nTargetType;

        return $this;
    }

    public function getNTargetId(): ?int
    {
        return $this->nTargetId;
    }

    public function setNTargetId(int $nTargetId): self
    {
        $this->nTargetId = $nTargetId;

        return $this;
    }

    public function getBTargetType(): ?bool
    {
        return $this->bTargetType;
    }

    public function setBTargetType(bool $bTargetType): self
    {
        $this->bTargetType = $bTargetType;

        return $this;
    }

    public function getBTargetId(): ?int
    {
        return $this->bTargetId;
    }

    public function setBTargetId(int $bTargetId): self
    {
        $this->bTargetId = $bTargetId;

        return $this;
    }


}
