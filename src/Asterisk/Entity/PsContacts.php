<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * PsContacts
 * @ApiResource()
 * @ORM\Table(name="ps_contacts",options={"collate"="utf8_general_ci"})
 * @ORM\Entity
 */
class PsContacts
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=255, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="uri", type="string", length=255, nullable=true)
     */
    private $uri;

    /**
     * @var int|null
     *
     * @ORM\Column(name="expiration_time", type="bigint", nullable=true)
     */
    private $expirationTime;

    /**
     * @var int|null
     *
     * @ORM\Column(name="qualify_frequency", type="integer", nullable=true)
     */
    private $qualifyFrequency;

    /**
     * @var string|null
     *
     * @ORM\Column(name="outbound_proxy", type="string", length=40, nullable=true)
     */
    private $outboundProxy;

    /**
     * @var string|null
     *
     * @ORM\Column(name="path", type="text", length=65535, nullable=true)
     */
    private $path;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_agent", type="string", length=255, nullable=true)
     */
    private $userAgent;

    /**
     * @var float|null
     *
     * @ORM\Column(name="qualify_timeout", type="float", precision=10, scale=0, nullable=true)
     */
    private $qualifyTimeout;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reg_server", type="string", length=20, nullable=true)
     */
    private $regServer;

    /**
     * @var string|null
     *
     * @ORM\Column(name="authenticate_qualify", type="string", length=0, nullable=true)
     */
    private $authenticateQualify;

    /**
     * @var string|null
     *
     * @ORM\Column(name="via_addr", type="string", length=40, nullable=true)
     */
    private $viaAddr;

    /**
     * @var int|null
     *
     * @ORM\Column(name="via_port", type="integer", nullable=true)
     */
    private $viaPort;

    /**
     * @var string|null
     *
     * @ORM\Column(name="call_id", type="string", length=255, nullable=true)
     */
    private $callId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="endpoint", type="string", length=40, nullable=true)
     */
    private $endpoint;

    /**
     * @var string|null
     *
     * @ORM\Column(name="prune_on_boot", type="string", length=0, nullable=true)
     */
    private $pruneOnBoot;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(?string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function getExpirationTime(): ?int
    {
        return $this->expirationTime;
    }

    public function setExpirationTime(?int $expirationTime): self
    {
        $this->expirationTime = $expirationTime;

        return $this;
    }

    public function getQualifyFrequency(): ?int
    {
        return $this->qualifyFrequency;
    }

    public function setQualifyFrequency(?int $qualifyFrequency): self
    {
        $this->qualifyFrequency = $qualifyFrequency;

        return $this;
    }

    public function getOutboundProxy(): ?string
    {
        return $this->outboundProxy;
    }

    public function setOutboundProxy(?string $outboundProxy): self
    {
        $this->outboundProxy = $outboundProxy;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getQualifyTimeout(): ?float
    {
        return $this->qualifyTimeout;
    }

    public function setQualifyTimeout(?float $qualifyTimeout): self
    {
        $this->qualifyTimeout = $qualifyTimeout;

        return $this;
    }

    public function getRegServer(): ?string
    {
        return $this->regServer;
    }

    public function setRegServer(?string $regServer): self
    {
        $this->regServer = $regServer;

        return $this;
    }

    public function getAuthenticateQualify(): ?string
    {
        return $this->authenticateQualify;
    }

    public function setAuthenticateQualify(?string $authenticateQualify): self
    {
        $this->authenticateQualify = $authenticateQualify;

        return $this;
    }

    public function getViaAddr(): ?string
    {
        return $this->viaAddr;
    }

    public function setViaAddr(?string $viaAddr): self
    {
        $this->viaAddr = $viaAddr;

        return $this;
    }

    public function getViaPort(): ?int
    {
        return $this->viaPort;
    }

    public function setViaPort(?int $viaPort): self
    {
        $this->viaPort = $viaPort;

        return $this;
    }

    public function getCallId(): ?string
    {
        return $this->callId;
    }

    public function setCallId(?string $callId): self
    {
        $this->callId = $callId;

        return $this;
    }

    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }

    public function setEndpoint(?string $endpoint): self
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function getPruneOnBoot(): ?string
    {
        return $this->pruneOnBoot;
    }

    public function setPruneOnBoot(?string $pruneOnBoot): self
    {
        $this->pruneOnBoot = $pruneOnBoot;

        return $this;
    }


}
