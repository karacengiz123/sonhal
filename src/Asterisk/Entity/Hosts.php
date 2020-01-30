<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Hosts
 * @ApiResource()
 * @ORM\Table(name="hosts",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\HostsRepository")
 * @Gedmo\Loggable()
 */
class Hosts
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
     * @ORM\Column(name="host", type="string", length=6, nullable=false, options={"default"="halo00","fixed"=true})
     * @Gedmo\Versioned()
     */
    private $host = 'halo00';

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
     * @ORM\Column(name="ssh_user", type="string", length=20, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $sshUser = '';

    /**
     * @var string
     *
     * @ORM\Column(name="ssh_pass", type="string", length=20, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $sshPass = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="out_ip", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $outIp;

    public function getIdx(): ?int
    {
        return $this->idx;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;

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

    public function getSshUser(): ?string
    {
        return $this->sshUser;
    }

    public function setSshUser(string $sshUser): self
    {
        $this->sshUser = $sshUser;

        return $this;
    }

    public function getSshPass(): ?string
    {
        return $this->sshPass;
    }

    public function setSshPass(string $sshPass): self
    {
        $this->sshPass = $sshPass;

        return $this;
    }

    public function getOutIp(): ?string
    {
        return $this->outIp;
    }

    public function setOutIp(?string $outIp): self
    {
        $this->outIp = $outIp;

        return $this;
    }


}
