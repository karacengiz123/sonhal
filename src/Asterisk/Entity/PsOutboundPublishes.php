<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * PsOutboundPublishes
 * @ApiResource()
 * @ORM\Table(name="ps_outbound_publishes",options={"collate"="utf8_general_ci"})
 * @ORM\Entity
 */
class PsOutboundPublishes
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
     * @var int|null
     *
     * @ORM\Column(name="expiration", type="integer", nullable=true)
     */
    private $expiration;

    /**
     * @var string|null
     *
     * @ORM\Column(name="outbound_auth", type="string", length=40, nullable=true)
     */
    private $outboundAuth;

    /**
     * @var string|null
     *
     * @ORM\Column(name="outbound_proxy", type="string", length=256, nullable=true)
     */
    private $outboundProxy;

    /**
     * @var string|null
     *
     * @ORM\Column(name="server_uri", type="string", length=256, nullable=true)
     */
    private $serverUri;

    /**
     * @var string|null
     *
     * @ORM\Column(name="from_uri", type="string", length=256, nullable=true)
     */
    private $fromUri;

    /**
     * @var string|null
     *
     * @ORM\Column(name="to_uri", type="string", length=256, nullable=true)
     */
    private $toUri;

    /**
     * @var string|null
     *
     * @ORM\Column(name="event", type="string", length=40, nullable=true)
     */
    private $event;

    /**
     * @var int|null
     *
     * @ORM\Column(name="max_auth_attempts", type="integer", nullable=true)
     */
    private $maxAuthAttempts;

    /**
     * @var string|null
     *
     * @ORM\Column(name="transport", type="string", length=40, nullable=true)
     */
    private $transport;

    /**
     * @var string|null
     *
     * @ORM\Column(name="multi_user", type="string", length=0, nullable=true)
     */
    private $multiUser;

    /**
     * @var string|null
     *
     * @ORM\Column(name="`@body`", type="string", length=40, nullable=true)
     */
    private $atBody;

    /**
     * @var string|null
     *
     * @ORM\Column(name="`@context`", type="string", length=256, nullable=true)
     */
    private $atContext;

    /**
     * @var string|null
     *
     * @ORM\Column(name="`@exten`", type="string", length=256, nullable=true)
     */
    private $atExten;

    public function getId(): ?string
    {
        return $this->id;
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

    public function getServerUri(): ?string
    {
        return $this->serverUri;
    }

    public function setServerUri(?string $serverUri): self
    {
        $this->serverUri = $serverUri;

        return $this;
    }

    public function getFromUri(): ?string
    {
        return $this->fromUri;
    }

    public function setFromUri(?string $fromUri): self
    {
        $this->fromUri = $fromUri;

        return $this;
    }

    public function getToUri(): ?string
    {
        return $this->toUri;
    }

    public function setToUri(?string $toUri): self
    {
        $this->toUri = $toUri;

        return $this;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function setEvent(?string $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getMaxAuthAttempts(): ?int
    {
        return $this->maxAuthAttempts;
    }

    public function setMaxAuthAttempts(?int $maxAuthAttempts): self
    {
        $this->maxAuthAttempts = $maxAuthAttempts;

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

    public function getMultiUser(): ?string
    {
        return $this->multiUser;
    }

    public function setMultiUser(?string $multiUser): self
    {
        $this->multiUser = $multiUser;

        return $this;
    }

    public function getAtBody(): ?string
    {
        return $this->atBody;
    }

    public function setAtBody(?string $atBody): self
    {
        $this->atBody = $atBody;

        return $this;
    }

    public function getAtContext(): ?string
    {
        return $this->atContext;
    }

    public function setAtContext(?string $atContext): self
    {
        $this->atContext = $atContext;

        return $this;
    }

    public function getAtExten(): ?string
    {
        return $this->atExten;
    }

    public function setAtExten(?string $atExten): self
    {
        $this->atExten = $atExten;

        return $this;
    }


}
