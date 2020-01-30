<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * PsAuths
 * @ApiResource()
 * @ORM\Table(name="ps_auths",options={"collate"="utf8_general_ci"})
 * @ORM\Entity
 */
class   PsAuths
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=40, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="auth_type", type="string", length=0, nullable=true)
     */
    private $authType;

    /**
     * @var int|null
     *
     * @ORM\Column(name="nonce_lifetime", type="integer", nullable=true)
     */
    private $nonceLifetime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="md5_cred", type="string", length=40, nullable=true)
     */
    private $md5Cred;

    /**
     * @var string|null
     *
     * @ORM\Column(name="password", type="string", length=80, nullable=true)
     */
    private $password;

    /**
     * @var string|null
     *
     * @ORM\Column(name="realm", type="string", length=40, nullable=true)
     */
    private $realm;

    /**
     * @var string|null
     *
     * @ORM\Column(name="username", type="string", length=40, nullable=true)
     */
    private $username;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getAuthType(): ?string
    {
        return $this->authType;
    }

    public function setAuthType(?string $authType): self
    {
        $this->authType = $authType;

        return $this;
    }

    public function getNonceLifetime(): ?int
    {
        return $this->nonceLifetime;
    }

    public function setNonceLifetime(?int $nonceLifetime): self
    {
        $this->nonceLifetime = $nonceLifetime;

        return $this;
    }

    public function getMd5Cred(): ?string
    {
        return $this->md5Cred;
    }

    public function setMd5Cred(?string $md5Cred): self
    {
        $this->md5Cred = $md5Cred;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRealm(): ?string
    {
        return $this->realm;
    }

    public function setRealm(?string $realm): self
    {
        $this->realm = $realm;

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


}
