<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * PsRegistrations
 * @ApiResource()
 * @ORM\Table(name="ps_registrations",options={"collate"="utf8_general_ci"})
 * @ORM\Entity
 */
class PsRegistrations
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
     * @ORM\Column(name="auth_rejection_permanent", type="string", length=0, nullable=true)
     */
    private $authRejectionPermanent;

    /**
     * @var string|null
     *
     * @ORM\Column(name="client_uri", type="string", length=255, nullable=true)
     */
    private $clientUri;

    /**
     * @var string|null
     *
     * @ORM\Column(name="contact_user", type="string", length=40, nullable=true)
     */
    private $contactUser;

    /**
     * @var int|null
     *
     * @ORM\Column(name="expiration", type="integer", nullable=true)
     */
    private $expiration;

    /**
     * @var int|null
     *
     * @ORM\Column(name="max_retries", type="integer", nullable=true)
     */
    private $maxRetries;

    /**
     * @var string|null
     *
     * @ORM\Column(name="outbound_auth", type="string", length=40, nullable=true)
     */
    private $outboundAuth;

    /**
     * @var string|null
     *
     * @ORM\Column(name="outbound_proxy", type="string", length=40, nullable=true)
     */
    private $outboundProxy;

    /**
     * @var int|null
     *
     * @ORM\Column(name="retry_interval", type="integer", nullable=true)
     */
    private $retryInterval;

    /**
     * @var int|null
     *
     * @ORM\Column(name="forbidden_retry_interval", type="integer", nullable=true)
     */
    private $forbiddenRetryInterval;

    /**
     * @var string|null
     *
     * @ORM\Column(name="server_uri", type="string", length=255, nullable=true)
     */
    private $serverUri;

    /**
     * @var string|null
     *
     * @ORM\Column(name="transport", type="string", length=40, nullable=true)
     */
    private $transport;

    /**
     * @var string|null
     *
     * @ORM\Column(name="support_path", type="string", length=0, nullable=true)
     */
    private $supportPath;

    /**
     * @var int|null
     *
     * @ORM\Column(name="fatal_retry_interval", type="integer", nullable=true)
     */
    private $fatalRetryInterval;

    /**
     * @var string|null
     *
     * @ORM\Column(name="line", type="string", length=0, nullable=true)
     */
    private $line;

    /**
     * @var string|null
     *
     * @ORM\Column(name="endpoint", type="string", length=40, nullable=true)
     */
    private $endpoint;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getAuthRejectionPermanent(): ?string
    {
        return $this->authRejectionPermanent;
    }

    public function setAuthRejectionPermanent(?string $authRejectionPermanent): self
    {
        $this->authRejectionPermanent = $authRejectionPermanent;

        return $this;
    }

    public function getClientUri(): ?string
    {
        return $this->clientUri;
    }

    public function setClientUri(?string $clientUri): self
    {
        $this->clientUri = $clientUri;

        return $this;
    }

    public function getContactUser(): ?string
    {
        return $this->contactUser;
    }

    public function setContactUser(?string $contactUser): self
    {
        $this->contactUser = $contactUser;

        return $this;
    }

    public function getExpiration(): ?int
    {
        return $this->expiration;
    }

    public function setExpiration(?int $expiration): self
    {
        $this->expiration = $expiration;

        return $this;
    }

    public function getMaxRetries(): ?int
    {
        return $this->maxRetries;
    }

    public function setMaxRetries(?int $maxRetries): self
    {
        $this->maxRetries = $maxRetries;

        return $this;
    }

    public function getOutboundAuth(): ?string
    {
        return $this->outboundAuth;
    }

    public function setOutboundAuth(?string $outboundAuth): self
    {
        $this->outboundAuth = $outboundAuth;

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

    public function getRetryInterval(): ?int
    {
        return $this->retryInterval;
    }

    public function setRetryInterval(?int $retryInterval): self
    {
        $this->retryInterval = $retryInterval;

        return $this;
    }

    public function getForbiddenRetryInterval(): ?int
    {
        return $this->forbiddenRetryInterval;
    }

    public function setForbiddenRetryInterval(?int $forbiddenRetryInterval): self
    {
        $this->forbiddenRetryInterval = $forbiddenRetryInterval;

        return $this;
    }

    public function getServerUri(): ?string
    {
        return $this->serverUri;
    }

    public function setServerUri(?string $serverUri): self
    {
        $this->serverUri = $serverUri;

        return $this;
    }

    public function getTransport(): ?string
    {
        return $this->transport;
    }

    public function setTransport(?string $transport): self
    {
        $this->transport = $transport;

        return $this;
    }

    public function getSupportPath(): ?string
    {
        return $this->supportPath;
    }

    public function setSupportPath(?string $supportPath): self
    {
        $this->supportPath = $supportPath;

        return $this;
    }

    public function getFatalRetryInterval(): ?int
    {
        return $this->fatalRetryInterval;
    }

    public function setFatalRetryInterval(?int $fatalRetryInterval): self
    {
        $this->fatalRetryInterval = $fatalRetryInterval;

        return $this;
    }

    public function getLine(): ?string
    {
        return $this->line;
    }

    public function setLine(?string $line): self
    {
        $this->line = $line;

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


}
