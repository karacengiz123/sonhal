<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * PsAors
 * @ApiResource()
 * @ORM\Table(name="ps_aors",options={"collate"="utf8_general_ci"})
 * @ORM\Entity
 */
class PsAors
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
     * @ORM\Column(name="contact", type="string", length=255, nullable=true)
     */
    private $contact;

    /**
     * @var int|null
     *
     * @ORM\Column(name="default_expiration", type="integer", nullable=true)
     */
    private $defaultExpiration;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mailboxes", type="string", length=80, nullable=true)
     */
    private $mailboxes;

    /**
     * @var int|null
     *
     * @ORM\Column(name="max_contacts", type="integer", nullable=true)
     */
    private $maxContacts;

    /**
     * @var int|null
     *
     * @ORM\Column(name="minimum_expiration", type="integer", nullable=true)
     */
    private $minimumExpiration;

    /**
     * @var string|null
     *
     * @ORM\Column(name="remove_existing", type="string", length=0, nullable=true)
     */
    private $removeExisting;

    /**
     * @var int|null
     *
     * @ORM\Column(name="qualify_frequency", type="integer", nullable=true)
     */
    private $qualifyFrequency;

    /**
     * @var string|null
     *
     * @ORM\Column(name="authenticate_qualify", type="string", length=0, nullable=true)
     */
    private $authenticateQualify;

    /**
     * @var int|null
     *
     * @ORM\Column(name="maximum_expiration", type="integer", nullable=true)
     */
    private $maximumExpiration;

    /**
     * @var string|null
     *
     * @ORM\Column(name="outbound_proxy", type="string", length=40, nullable=true)
     */
    private $outboundProxy;

    /**
     * @var string|null
     *
     * @ORM\Column(name="support_path", type="string", length=0, nullable=true)
     */
    private $supportPath;

    /**
     * @var float|null
     *
     * @ORM\Column(name="qualify_timeout", type="float", precision=10, scale=0, nullable=true)
     */
    private $qualifyTimeout;

    /**
     * @var string|null
     *
     * @ORM\Column(name="voicemail_extension", type="string", length=40, nullable=true)
     */
    private $voicemailExtension;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getDefaultExpiration(): ?int
    {
        return $this->defaultExpiration;
    }

    public function setDefaultExpiration(?int $defaultExpiration): self
    {
        $this->defaultExpiration = $defaultExpiration;

        return $this;
    }

    public function getMailboxes(): ?string
    {
        return $this->mailboxes;
    }

    public function setMailboxes(?string $mailboxes): self
    {
        $this->mailboxes = $mailboxes;

        return $this;
    }

    public function getMaxContacts(): ?int
    {
        return $this->maxContacts;
    }

    public function setMaxContacts(?int $maxContacts): self
    {
        $this->maxContacts = $maxContacts;

        return $this;
    }

    public function getMinimumExpiration(): ?int
    {
        return $this->minimumExpiration;
    }

    public function setMinimumExpiration(?int $minimumExpiration): self
    {
        $this->minimumExpiration = $minimumExpiration;

        return $this;
    }

    public function getRemoveExisting(): ?string
    {
        return $this->removeExisting;
    }

    public function setRemoveExisting(?string $removeExisting): self
    {
        $this->removeExisting = $removeExisting;

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

    public function getAuthenticateQualify(): ?string
    {
        return $this->authenticateQualify;
    }

    public function setAuthenticateQualify(?string $authenticateQualify): self
    {
        $this->authenticateQualify = $authenticateQualify;

        return $this;
    }

    public function getMaximumExpiration(): ?int
    {
        return $this->maximumExpiration;
    }

    public function setMaximumExpiration(?int $maximumExpiration): self
    {
        $this->maximumExpiration = $maximumExpiration;

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

    public function getSupportPath(): ?string
    {
        return $this->supportPath;
    }

    public function setSupportPath(?string $supportPath): self
    {
        $this->supportPath = $supportPath;

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

    public function getVoicemailExtension(): ?string
    {
        return $this->voicemailExtension;
    }

    public function setVoicemailExtension(?string $voicemailExtension): self
    {
        $this->voicemailExtension = $voicemailExtension;

        return $this;
    }


}
