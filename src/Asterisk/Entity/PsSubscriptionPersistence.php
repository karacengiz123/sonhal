<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * PsSubscriptionPersistence
 * @ApiResource()
 * @ORM\Table(name="ps_subscription_persistence",options={"collate"="utf8_general_ci"})
 * @ORM\Entity
 */
class PsSubscriptionPersistence
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
     * @ORM\Column(name="packet", type="string", length=2048, nullable=true)
     */
    private $packet;

    /**
     * @var string|null
     *
     * @ORM\Column(name="src_name", type="string", length=128, nullable=true)
     */
    private $srcName;

    /**
     * @var int|null
     *
     * @ORM\Column(name="src_port", type="integer", nullable=true)
     */
    private $srcPort;

    /**
     * @var string|null
     *
     * @ORM\Column(name="transport_key", type="string", length=64, nullable=true)
     */
    private $transportKey;

    /**
     * @var string|null
     *
     * @ORM\Column(name="local_name", type="string", length=128, nullable=true)
     */
    private $localName;

    /**
     * @var int|null
     *
     * @ORM\Column(name="local_port", type="integer", nullable=true)
     */
    private $localPort;

    /**
     * @var int|null
     *
     * @ORM\Column(name="cseq", type="integer", nullable=true)
     */
    private $cseq;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tag", type="string", length=128, nullable=true)
     */
    private $tag;

    /**
     * @var string|null
     *
     * @ORM\Column(name="endpoint", type="string", length=40, nullable=true)
     */
    private $endpoint;

    /**
     * @var int|null
     *
     * @ORM\Column(name="expires", type="integer", nullable=true)
     */
    private $expires;

    /**
     * @var string|null
     *
     * @ORM\Column(name="contact_uri", type="string", length=256, nullable=true)
     */
    private $contactUri;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPacket(): ?string
    {
        return $this->packet;
    }

    public function setPacket(?string $packet): self
    {
        $this->packet = $packet;

        return $this;
    }

    public function getSrcName(): ?string
    {
        return $this->srcName;
    }

    public function setSrcName(?string $srcName): self
    {
        $this->srcName = $srcName;

        return $this;
    }

    public function getSrcPort(): ?int
    {
        return $this->srcPort;
    }

    public function setSrcPort(?int $srcPort): self
    {
        $this->srcPort = $srcPort;

        return $this;
    }

    public function getTransportKey(): ?string
    {
        return $this->transportKey;
    }

    public function setTransportKey(?string $transportKey): self
    {
        $this->transportKey = $transportKey;

        return $this;
    }

    public function getLocalName(): ?string
    {
        return $this->localName;
    }

    public function setLocalName(?string $localName): self
    {
        $this->localName = $localName;

        return $this;
    }

    public function getLocalPort(): ?int
    {
        return $this->localPort;
    }

    public function setLocalPort(?int $localPort): self
    {
        $this->localPort = $localPort;

        return $this;
    }

    public function getCseq(): ?int
    {
        return $this->cseq;
    }

    public function setCseq(?int $cseq): self
    {
        $this->cseq = $cseq;

        return $this;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(?string $tag): self
    {
        $this->tag = $tag;

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

    public function getExpires(): ?int
    {
        return $this->expires;
    }

    public function setExpires(?int $expires): self
    {
        $this->expires = $expires;

        return $this;
    }

    public function getContactUri(): ?string
    {
        return $this->contactUri;
    }

    public function setContactUri(?string $contactUri): self
    {
        $this->contactUri = $contactUri;

        return $this;
    }


}
