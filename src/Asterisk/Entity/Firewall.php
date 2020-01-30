<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Firewall
 * @ApiResource()
 * @ORM\Table(name="firewall",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\FirewallRepository")
 * @Gedmo\Loggable()
 */
class Firewall
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
     * @ORM\Column(name="ast_id", type="string", length=10, nullable=false, options={"default"="dast00","fixed"=true})
     * @Gedmo\Versioned()
     */
    private $astId = 'dast00';

    /**
     * @var int|null
     *
     * @ORM\Column(name="cus_id", type="integer", nullable=true)
     * @Gedmo\Versioned()
     */
    private $cusId;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_addr", type="string", length=50, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $ipAddr = '';

    /**
     * @var string
     *
     * @ORM\Column(name="maskbits", type="string", length=5, nullable=false, options={"default"="32","fixed"=true})
     * @Gedmo\Versioned()
     */
    private $maskbits = '32';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     * @Gedmo\Versioned()
     */
    private $description = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     * @Gedmo\Versioned()
     */
    private $status = '0';

    public function getIdx(): ?int
    {
        return $this->idx;
    }

    public function getAstId(): ?string
    {
        return $this->astId;
    }

    public function setAstId(string $astId): self
    {
        $this->astId = $astId;

        return $this;
    }

    public function getCusId(): ?int
    {
        return $this->cusId;
    }

    public function setCusId(?int $cusId): self
    {
        $this->cusId = $cusId;

        return $this;
    }

    public function getIpAddr(): ?string
    {
        return $this->ipAddr;
    }

    public function setIpAddr(string $ipAddr): self
    {
        $this->ipAddr = $ipAddr;

        return $this;
    }

    public function getMaskbits(): ?string
    {
        return $this->maskbits;
    }

    public function setMaskbits(string $maskbits): self
    {
        $this->maskbits = $maskbits;

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

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }


}
