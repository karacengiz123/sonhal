<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * CustomDialplan
 * @ApiResource()
 * @ORM\Table(name="custom_dialplan",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\CustomDialplanRepository")
 * @Gedmo\Loggable()
 */
class CustomDialplan
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
     * @ORM\Column(name="scode", type="string", length=5, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $scode = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="dialplan", type="text", length=65535, nullable=false)
     * @Gedmo\Versioned()
     */
    private $dialplan;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDialplan(): ?string
    {
        return $this->dialplan;
    }

    public function setDialplan(string $dialplan): self
    {
        $this->dialplan = $dialplan;

        return $this;
    }


}
