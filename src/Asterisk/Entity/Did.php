<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Did
 * @ApiResource()
 * @ORM\Table(name="did",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\DidRepository")
 * @Gedmo\Loggable()
 */
class Did
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
     * @ORM\Column(name="cus_id", type="string", length=7, nullable=false, options={"default"="9340000","fixed"=true})
     * @Gedmo\Versioned()
     */
    private $cusId = '9340000';

    /**
     * @var string
     *
     * @ORM\Column(name="did", type="string", length=11, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $did = '';

    /**
     * @var string
     *
     * @ORM\Column(name="clid", type="string", length=50, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $clid = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="diversion", type="boolean", nullable=false)
     * @Gedmo\Versioned()
     */
    private $diversion = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="dclid", type="string", length=12, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $dclid = '';

    /**
     * @var string
     *
     * @ORM\Column(name="prefix", type="string", length=10, nullable=false, options={"default"="90","fixed"=true})
     * @Gedmo\Versioned()
     */
    private $prefix = '90';

    /**
     * @var int
     *
     * @ORM\Column(name="trunk", type="integer", nullable=false, options={"default"="1"})
     * @Gedmo\Versioned()
     */
    private $trunk = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $description = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="target_type", type="boolean", nullable=false, options={"comment"="0: hangup, 1: ivr, 2: queue, 3: exten, 4: tc, 5: announcement, 6: custom_exten, 99: custom dialplan"})
     * @Gedmo\Versioned()
     */
    private $targetType = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="target_id", type="bigint", nullable=false)
     * @Gedmo\Versioned()
     */
    private $targetId = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="crm", type="boolean", nullable=false)
     * @Gedmo\Versioned()
     */
    private $crm = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="status", type="boolean", nullable=true, options={"default"="1","comment"="0: pasif, 1: aktif"})
     * @Gedmo\Versioned()
     */
    private $status = '1';

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

    public function getDid(): ?string
    {
        return $this->did;
    }

    public function setDid(string $did): self
    {
        $this->did = $did;

        return $this;
    }

    public function getClid(): ?string
    {
        return $this->clid;
    }

    public function setClid(string $clid): self
    {
        $this->clid = $clid;

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

    public function getDclid(): ?string
    {
        return $this->dclid;
    }

    public function setDclid(string $dclid): self
    {
        $this->dclid = $dclid;

        return $this;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function getTrunk(): ?int
    {
        return $this->trunk;
    }

    public function setTrunk(int $trunk): self
    {
        $this->trunk = $trunk;

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

    public function getTargetType(): ?bool
    {
        return $this->targetType;
    }

    public function setTargetType(bool $targetType): self
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

    public function getCrm(): ?bool
    {
        return $this->crm;
    }

    public function setCrm(bool $crm): self
    {
        $this->crm = $crm;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }


}
