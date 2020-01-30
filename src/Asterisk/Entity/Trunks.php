<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Trunks
 * @ApiResource()
 * @ORM\Table(name="trunks",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\TrunksRepository")
 * @Gedmo\Loggable()
 */
class Trunks
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
     * @ORM\Column(name="name", type="string", length=50, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $name = '';

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $status = '';

    /**
     * @var string
     *
     * @ORM\Column(name="details", type="text", length=65535, nullable=false)
     * @Gedmo\Versioned()
     */
    private $details;

    /**
     * @var bool
     *
     * @ORM\Column(name="rs", type="boolean", nullable=false)
     * @Gedmo\Versioned()
     */
    private $rs = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $username;

    /**
     * @var string|null
     *
     * @ORM\Column(name="secret", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $secret;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ip", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $ip;

    /**
     * @var string|null
     *
     * @ORM\Column(name="did", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $did;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tech", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $tech;

    /**
     * @var string
     *
     * @ORM\Column(name="dialoutprefix", type="string", length=50, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $dialoutprefix = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="channelid", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $channelid;

    /**
     * @var int
     *
     * @ORM\Column(name="cps", type="integer", nullable=false, options={"default"="100"})
     * @Gedmo\Versioned()
     */
    private $cps = '100';

    public function getIdx(): ?int
    {
        return $this->idx;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getRs(): ?bool
    {
        return $this->rs;
    }

    public function setRs(bool $rs): self
    {
        $this->rs = $rs;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function setSecret(?string $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getDid(): ?string
    {
        return $this->did;
    }

    public function setDid(?string $did): self
    {
        $this->did = $did;

        return $this;
    }

    public function getTech(): ?string
    {
        return $this->tech;
    }

    public function setTech(?string $tech): self
    {
        $this->tech = $tech;

        return $this;
    }

    public function getDialoutprefix(): ?string
    {
        return $this->dialoutprefix;
    }

    public function setDialoutprefix(string $dialoutprefix): self
    {
        $this->dialoutprefix = $dialoutprefix;

        return $this;
    }

    public function getChannelid(): ?string
    {
        return $this->channelid;
    }

    public function setChannelid(?string $channelid): self
    {
        $this->channelid = $channelid;

        return $this;
    }

    public function getCps(): ?int
    {
        return $this->cps;
    }

    public function setCps(int $cps): self
    {
        $this->cps = $cps;

        return $this;
    }


}
