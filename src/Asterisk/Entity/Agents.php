<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Agents
 * @ORM\Table(name="agents",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\AgentsRepository")
 * @ApiResource()
 * @Gedmo\Loggable()
 */
class  Agents
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
     * @ORM\Column(name="username", type="string", length=20, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $username = '';

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=20, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $password = '';

    /**
     * @var string
     *
     * @ORM\Column(name="exten", type="string", length=10, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $exten = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="loggedin", type="boolean", nullable=false, options={"comment"="-1: failed, 0: no, 1: yes"})
     * @Gedmo\Versioned()
     */
    private $loggedin = '0';

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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getExten(): ?string
    {
        return $this->exten;
    }

    public function setExten(string $exten): self
    {
        $this->exten = $exten;

        return $this;
    }

    public function getLoggedin(): ?bool
    {
        return $this->loggedin;
    }

    public function setLoggedin(bool $loggedin): self
    {
        $this->loggedin = $loggedin;

        return $this;
    }



}
