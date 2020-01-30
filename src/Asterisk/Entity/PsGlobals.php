<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * PsGlobals
 * @ApiResource()
 * @ORM\Table(name="ps_globals",options={"collate"="utf8_general_ci"})
 * @ORM\Entity
 */
class PsGlobals
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
     * @ORM\Column(name="max_forwards", type="integer", nullable=true)
     */
    private $maxForwards;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_agent", type="string", length=255, nullable=true)
     */
    private $userAgent;

    /**
     * @var string|null
     *
     * @ORM\Column(name="default_outbound_endpoint", type="string", length=40, nullable=true)
     */
    private $defaultOutboundEndpoint;

    /**
     * @var string|null
     *
     * @ORM\Column(name="debug", type="string", length=40, nullable=true)
     */
    private $debug;

    /**
     * @var string|null
     *
     * @ORM\Column(name="endpoint_identifier_order", type="string", length=40, nullable=true)
     */
    private $endpointIdentifierOrder;

    /**
     * @var int|null
     *
     * @ORM\Column(name="max_initial_qualify_time", type="integer", nullable=true)
     */
    private $maxInitialQualifyTime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="default_from_user", type="string", length=80, nullable=true)
     */
    private $defaultFromUser;

    /**
     * @var int|null
     *
     * @ORM\Column(name="keep_alive_interval", type="integer", nullable=true)
     */
    private $keepAliveInterval;

    /**
     * @var string|null
     *
     * @ORM\Column(name="regcontext", type="string", length=80, nullable=true)
     */
    private $regcontext;

    /**
     * @var int|null
     *
     * @ORM\Column(name="contact_expiration_check_interval", type="integer", nullable=true)
     */
    private $contactExpirationCheckInterval;

    /**
     * @var string|null
     *
     * @ORM\Column(name="default_voicemail_extension", type="string", length=40, nullable=true)
     */
    private $defaultVoicemailExtension;

    /**
     * @var string|null
     *
     * @ORM\Column(name="disable_multi_domain", type="string", length=0, nullable=true)
     */
    private $disableMultiDomain;

    /**
     * @var int|null
     *
     * @ORM\Column(name="unidentified_request_count", type="integer", nullable=true)
     */
    private $unidentifiedRequestCount;

    /**
     * @var int|null
     *
     * @ORM\Column(name="unidentified_request_period", type="integer", nullable=true)
     */
    private $unidentifiedRequestPeriod;

    /**
     * @var int|null
     *
     * @ORM\Column(name="unidentified_request_prune_interval", type="integer", nullable=true)
     */
    private $unidentifiedRequestPruneInterval;

    /**
     * @var string|null
     *
     * @ORM\Column(name="default_realm", type="string", length=40, nullable=true)
     */
    private $defaultRealm;

    /**
     * @var int|null
     *
     * @ORM\Column(name="mwi_tps_queue_high", type="integer", nullable=true)
     */
    private $mwiTpsQueueHigh;

    /**
     * @var int|null
     *
     * @ORM\Column(name="mwi_tps_queue_low", type="integer", nullable=true)
     */
    private $mwiTpsQueueLow;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mwi_disable_initial_unsolicited", type="string", length=0, nullable=true)
     */
    private $mwiDisableInitialUnsolicited;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ignore_uri_user_options", type="string", length=0, nullable=true)
     */
    private $ignoreUriUserOptions;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getMaxForwards(): ?int
    {
        return $this->maxForwards;
    }

    public function setMaxForwards(?int $maxForwards): self
    {
        $this->maxForwards = $maxForwards;

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

    public function getDefaultOutboundEndpoint(): ?string
    {
        return $this->defaultOutboundEndpoint;
    }

    public function setDefaultOutboundEndpoint(?string $defaultOutboundEndpoint): self
    {
        $this->defaultOutboundEndpoint = $defaultOutboundEndpoint;

        return $this;
    }

    public function getDebug(): ?string
    {
        return $this->debug;
    }

    public function setDebug(?string $debug): self
    {
        $this->debug = $debug;

        return $this;
    }

    public function getEndpointIdentifierOrder(): ?string
    {
        return $this->endpointIdentifierOrder;
    }

    public function setEndpointIdentifierOrder(?string $endpointIdentifierOrder): self
    {
        $this->endpointIdentifierOrder = $endpointIdentifierOrder;

        return $this;
    }

    public function getMaxInitialQualifyTime(): ?int
    {
        return $this->maxInitialQualifyTime;
    }

    public function setMaxInitialQualifyTime(?int $maxInitialQualifyTime): self
    {
        $this->maxInitialQualifyTime = $maxInitialQualifyTime;

        return $this;
    }

    public function getDefaultFromUser(): ?string
    {
        return $this->defaultFromUser;
    }

    public function setDefaultFromUser(?string $defaultFromUser): self
    {
        $this->defaultFromUser = $defaultFromUser;

        return $this;
    }

    public function getKeepAliveInterval(): ?int
    {
        return $this->keepAliveInterval;
    }

    public function setKeepAliveInterval(?int $keepAliveInterval): self
    {
        $this->keepAliveInterval = $keepAliveInterval;

        return $this;
    }

    public function getRegcontext(): ?string
    {
        return $this->regcontext;
    }

    public function setRegcontext(?string $regcontext): self
    {
        $this->regcontext = $regcontext;

        return $this;
    }

    public function getContactExpirationCheckInterval(): ?int
    {
        return $this->contactExpirationCheckInterval;
    }

    public function setContactExpirationCheckInterval(?int $contactExpirationCheckInterval): self
    {
        $this->contactExpirationCheckInterval = $contactExpirationCheckInterval;

        return $this;
    }

    public function getDefaultVoicemailExtension(): ?string
    {
        return $this->defaultVoicemailExtension;
    }

    public function setDefaultVoicemailExtension(?string $defaultVoicemailExtension): self
    {
        $this->defaultVoicemailExtension = $defaultVoicemailExtension;

        return $this;
    }

    public function getDisableMultiDomain(): ?string
    {
        return $this->disableMultiDomain;
    }

    public function setDisableMultiDomain(?string $disableMultiDomain): self
    {
        $this->disableMultiDomain = $disableMultiDomain;

        return $this;
    }

    public function getUnidentifiedRequestCount(): ?int
    {
        return $this->unidentifiedRequestCount;
    }

    public function setUnidentifiedRequestCount(?int $unidentifiedRequestCount): self
    {
        $this->unidentifiedRequestCount = $unidentifiedRequestCount;

        return $this;
    }

    public function getUnidentifiedRequestPeriod(): ?int
    {
        return $this->unidentifiedRequestPeriod;
    }

    public function setUnidentifiedRequestPeriod(?int $unidentifiedRequestPeriod): self
    {
        $this->unidentifiedRequestPeriod = $unidentifiedRequestPeriod;

        return $this;
    }

    public function getUnidentifiedRequestPruneInterval(): ?int
    {
        return $this->unidentifiedRequestPruneInterval;
    }

    public function setUnidentifiedRequestPruneInterval(?int $unidentifiedRequestPruneInterval): self
    {
        $this->unidentifiedRequestPruneInterval = $unidentifiedRequestPruneInterval;

        return $this;
    }

    public function getDefaultRealm(): ?string
    {
        return $this->defaultRealm;
    }

    public function setDefaultRealm(?string $defaultRealm): self
    {
        $this->defaultRealm = $defaultRealm;

        return $this;
    }

    public function getMwiTpsQueueHigh(): ?int
    {
        return $this->mwiTpsQueueHigh;
    }

    public function setMwiTpsQueueHigh(?int $mwiTpsQueueHigh): self
    {
        $this->mwiTpsQueueHigh = $mwiTpsQueueHigh;

        return $this;
    }

    public function getMwiTpsQueueLow(): ?int
    {
        return $this->mwiTpsQueueLow;
    }

    public function setMwiTpsQueueLow(?int $mwiTpsQueueLow): self
    {
        $this->mwiTpsQueueLow = $mwiTpsQueueLow;

        return $this;
    }

    public function getMwiDisableInitialUnsolicited(): ?string
    {
        return $this->mwiDisableInitialUnsolicited;
    }

    public function setMwiDisableInitialUnsolicited(?string $mwiDisableInitialUnsolicited): self
    {
        $this->mwiDisableInitialUnsolicited = $mwiDisableInitialUnsolicited;

        return $this;
    }

    public function getIgnoreUriUserOptions(): ?string
    {
        return $this->ignoreUriUserOptions;
    }

    public function setIgnoreUriUserOptions(?string $ignoreUriUserOptions): self
    {
        $this->ignoreUriUserOptions = $ignoreUriUserOptions;

        return $this;
    }


}
