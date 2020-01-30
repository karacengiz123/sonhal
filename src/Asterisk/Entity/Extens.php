<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Extens
 * @ApiResource()
 * @ORM\Table(name="extens",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\ExtensRepository")
 * @Gedmo\Loggable()
 */
class Extens
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="idx", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idx;

    /**
     * @var string
     * @ORM\Column(name="cus_id", type="string", length=7, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $cusId = '';

    /**
     * @var string
     *
     * @ORM\Column(name="tech", type="string", length=6, nullable=false, options={"default"="SIP","fixed"=true})
     * @Gedmo\Versioned()
     */
    private $tech = 'SIP';

    /**
     * @var string
     * @ORM\Column(name="exten", type="string")
     * @Gedmo\Versioned()
     */
    private $exten = '';

    /**
     * @var string
     *
     * @ORM\Column(name="secret", type="string", length=20, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $secret = '';

    /**
     * @var int
     *
     * @ORM\Column(name="timeout", type="integer", nullable=false, options={"default"="30"})
     * @Gedmo\Versioned()
     */
    private $timeout = '30';

    /**
     * @var string
     *
     * @ORM\Column(name="caller_id", type="string", length=64, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $callerId = '';

    /**
     * @var string
     *
     * @ORM\Column(name="prefix", type="string", length=3, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $prefix = '';

    /**
     * @var string
     *
     * @ORM\Column(name="keyword", type="string", length=255, nullable=false)
     * @Gedmo\Versioned()
     */
    private $keyword = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="cp_group", type="string", length=10, nullable=true, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $cpGroup;

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
     * @ORM\Column(name="ip_addr", type="string", length=15, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $ipAddr = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="recording_in", type="boolean", nullable=false, options={"default"="1","comment"="0: disable, 1: enable"})
     * @Gedmo\Versioned()
     */
    private $recordingIn = '1';

    /**
     * @var bool
     *
     * @ORM\Column(name="recording_out", type="boolean", nullable=false, options={"default"="1","comment"="0: disable, 1: enable"})
     * @Gedmo\Versioned()
     */
    private $recordingOut = '1';

    /**
     * @var int
     *
     * @ORM\Column(name="default_did", type="integer", nullable=false, options={"comment"="customer did id"})
     * @Gedmo\Versioned()
     */
    private $defaultDid = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="second_did", type="integer", nullable=false, options={"comment"="customer did id"})
     * @Gedmo\Versioned()
     */
    private $secondDid = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="a_target_type", type="boolean", nullable=false, options={"default"="-1","comment"="0: hangup, 1: ivr, 2: queue, 3: exten, 4: tc, 5: announcement"})
     * @Gedmo\Versioned()
     */
    private $aTargetType = '-1';

    /**
     * @var int
     *
     * @ORM\Column(name="a_target_id", type="bigint", nullable=false)
     * @Gedmo\Versioned()
     */
    private $aTargetId = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="n_target_type", type="boolean", nullable=false, options={"comment"="0: hangup, 1: ivr, 2: queue, 3: exten, 4: tc, 5: announcement"})
     * @Gedmo\Versioned()
     */
    private $nTargetType = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="n_target_id", type="bigint", nullable=false)
     * @Gedmo\Versioned()
     */
    private $nTargetId = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="b_target_type", type="boolean", nullable=false, options={"comment"="0: hangup, 1: ivr, 2: queue, 3: exten, 4: tc, 5: announcement"})
     * @Gedmo\Versioned()
     */
    private $bTargetType = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="b_target_id", type="bigint", nullable=false)
     * @Gedmo\Versioned()
     */
    private $bTargetId = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="c_target_type", type="boolean", nullable=false, options={"comment"="0: hangup, 1: ivr, 2: queue, 3: exten, 4: tc, 5: announcement"})
     * @Gedmo\Versioned()
     */
    private $cTargetType = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="c_target_id", type="bigint", nullable=false)
     * @Gedmo\Versioned()
     */
    private $cTargetId = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="auth_local", type="boolean", nullable=false, options={"default"="1"})
     * @Gedmo\Versioned()
     */
    private $authLocal = '1';

    /**
     * @var bool
     *
     * @ORM\Column(name="auth_national", type="boolean", nullable=false, options={"default"="1"})
     * @Gedmo\Versioned()
     */
    private $authNational = '1';

    /**
     * @var bool
     *
     * @ORM\Column(name="auth_mobile", type="boolean", nullable=false, options={"default"="1"})
     * @Gedmo\Versioned()
     */
    private $authMobile = '1';

    /**
     * @var bool
     *
     * @ORM\Column(name="auth_global", type="boolean", nullable=false)
     * @Gedmo\Versioned()
     */
    private $authGlobal = '0';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="extens")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    public function getIdx(): ?int
    {
        return $this->idx;
    }

    public function getCusId(): ?string
    {
        return $this->cusId;
    }

    public function setCusId(string $cusId): self
    {
        $this->cusId = $cusId;

        return $this;
    }

    public function getTech(): ?string
    {
        return $this->tech;
    }

    public function setTech(string $tech): self
    {
        $this->tech = $tech;

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

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function setSecret(string $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

    public function getTimeout(): ?int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function getCallerId(): ?string
    {
        return $this->callerId;
    }

    public function setCallerId(string $callerId): self
    {
        $this->callerId = $callerId;

        return $this;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function setKeyword(string $keyword): self
    {
        $this->keyword = $keyword;

        return $this;
    }

    public function getCpGroup(): ?string
    {
        return $this->cpGroup;
    }

    public function setCpGroup(?string $cpGroup): self
    {
        $this->cpGroup = $cpGroup;

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

    public function getIpAddr(): ?string
    {
        return $this->ipAddr;
    }

    public function setIpAddr(string $ipAddr): self
    {
        $this->ipAddr = $ipAddr;

        return $this;
    }

    public function getRecordingIn(): ?bool
    {
        return $this->recordingIn;
    }

    public function setRecordingIn(bool $recordingIn): self
    {
        $this->recordingIn = $recordingIn;

        return $this;
    }

    public function getRecordingOut(): ?bool
    {
        return $this->recordingOut;
    }

    public function setRecordingOut(bool $recordingOut): self
    {
        $this->recordingOut = $recordingOut;

        return $this;
    }

    public function getDefaultDid(): ?int
    {
        return $this->defaultDid;
    }

    public function setDefaultDid(int $defaultDid): self
    {
        $this->defaultDid = $defaultDid;

        return $this;
    }

    public function getSecondDid(): ?int
    {
        return $this->secondDid;
    }

    public function setSecondDid(int $secondDid): self
    {
        $this->secondDid = $secondDid;

        return $this;
    }

    public function getATargetType(): ?bool
    {
        return $this->aTargetType;
    }

    public function setATargetType(bool $aTargetType): self
    {
        $this->aTargetType = $aTargetType;

        return $this;
    }

    public function getATargetId(): ?int
    {
        return $this->aTargetId;
    }

    public function setATargetId(int $aTargetId): self
    {
        $this->aTargetId = $aTargetId;

        return $this;
    }

    public function getNTargetType(): ?bool
    {
        return $this->nTargetType;
    }

    public function setNTargetType(bool $nTargetType): self
    {
        $this->nTargetType = $nTargetType;

        return $this;
    }

    public function getNTargetId(): ?int
    {
        return $this->nTargetId;
    }

    public function setNTargetId(int $nTargetId): self
    {
        $this->nTargetId = $nTargetId;

        return $this;
    }

    public function getBTargetType(): ?bool
    {
        return $this->bTargetType;
    }

    public function setBTargetType(bool $bTargetType): self
    {
        $this->bTargetType = $bTargetType;

        return $this;
    }

    public function getBTargetId(): ?int
    {
        return $this->bTargetId;
    }

    public function setBTargetId(int $bTargetId): self
    {
        $this->bTargetId = $bTargetId;

        return $this;
    }

    public function getCTargetType(): ?bool
    {
        return $this->cTargetType;
    }

    public function setCTargetType(bool $cTargetType): self
    {
        $this->cTargetType = $cTargetType;

        return $this;
    }

    public function getCTargetId(): ?int
    {
        return $this->cTargetId;
    }

    public function setCTargetId(int $cTargetId): self
    {
        $this->cTargetId = $cTargetId;

        return $this;
    }

    public function getAuthLocal(): ?bool
    {
        return $this->authLocal;
    }

    public function setAuthLocal(bool $authLocal): self
    {
        $this->authLocal = $authLocal;

        return $this;
    }

    public function getAuthNational(): ?bool
    {
        return $this->authNational;
    }

    public function setAuthNational(bool $authNational): self
    {
        $this->authNational = $authNational;

        return $this;
    }

    public function getAuthMobile(): ?bool
    {
        return $this->authMobile;
    }

    public function setAuthMobile(bool $authMobile): self
    {
        $this->authMobile = $authMobile;

        return $this;
    }

    public function getAuthGlobal(): ?bool
    {
        return $this->authGlobal;
    }

    public function setAuthGlobal(bool $authGlobal): self
    {
        $this->authGlobal = $authGlobal;

        return $this;
    }

    public function setIdx(int $idx): self
    {
        $this->idx = $idx;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

}
