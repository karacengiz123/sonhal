<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ApiResource()
 * Calls
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\CallsRepository")
 * @ORM\Table(options={"collate"="utf8_general_ci"}, name="calls", indexes={@ORM\Index(name="i_call_id", columns={"call_id"}),
 *     @ORM\Index(name="i_call_status", columns={"call_status"}),
 *     @ORM\Index(name="i_call_type", columns={"call_type"}),
 *     @ORM\Index(name="i_dt", columns={"dt"}),
 *     @ORM\Index(name="i_did", columns={"did"}),
 *     @ORM\Index(name="i_clid", columns={"clid"}),
 *     @ORM\Index(name="i_channel_id", columns={"channel_id"}),
 *     @ORM\Index(name="i_dt_queue", columns={"dt_queue"}),
 *     @ORM\Index(name="i_queue", columns={"queue"}),
 *     @ORM\Index(name="i_dt_exten", columns={"dt_exten"}),
 *     @ORM\Index(name="i_exten", columns={"exten"}),
 *     @ORM\Index(name="i_dt_hangup", columns={"dt_hangup"}),
 *     @ORM\Index(name="i_userfield", columns={"userfield"}),
 *     @ORM\Index(name="i_exten_channel_id", columns={"exten_channel_id"}),
 *     @ORM\Index(name="i_userfield", columns={"userfield"}),
 *     @ORM\Index(name="i_exten_channel_id", columns={"exten_channel_id"}),
 *     @ORM\Index(name="i_dur_ivr", columns={"dur_ivr"}),
 *     @ORM\Index(name="i_dur_queue", columns={"dur_queue"}),
 *     @ORM\Index(name="i_user_id", columns={"user_id"}),
 *     @ORM\Index(name="i_who_completed", columns={"who_completed"}),
 *     @ORM\Index(name="i_dur_exten", columns={"dur_exten"}),
 * })
 * @ORM\Entity
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogCalls")
 *
 */
class Calls
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
     * @var string|null
     *
     * @ORM\Column(name="call_type", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $callType;

    /**
     * @var string
     *
     * @ORM\Column(name="call_id", type="string", length=255, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $callId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt", type="datetime", nullable=false)
     * @Gedmo\Versioned()
     */
    private $dt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="did", type="string", length=10, nullable=true, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $did;

    /**
     * @var string|null
     *
     * @ORM\Column(name="clid", type="string", length=255, nullable=true, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $clid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="channel_id", type="string", length=255, nullable=true, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $channelId;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dt_queue", type="datetime", nullable=true)
     * @Gedmo\Versioned()
     */
    private $dtQueue;

    /**
     * @var string|null
     *
     * @ORM\Column(name="queue", type="string", length=9, nullable=true, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $queue;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dt_exten", type="datetime", nullable=true)
     * @Gedmo\Versioned()
     */
    private $dtExten;

    /**
     * @var string|null
     *
     * @ORM\Column(name="exten", type="string", length=10, nullable=true, options={"fixed"=true, "default"=NULL})
     * @Gedmo\Versioned()
     */
    private $exten;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dt_hangup", type="datetime", nullable=true)
     * @Gedmo\Versioned()
     */
    private $dtHangup;

    /**
     * @var string
     *
     * @ORM\Column(name="call_status", type="string", length=255, nullable=false, options={"default"="Active"})
     * @Gedmo\Versioned()
     */
    private $callStatus = 'Active';

    /**
     * @var string|null
     *
     * @ORM\Column(name="userfield", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $userfield;

    /**
     * @var string|null
     *
     * @ORM\Column(name="exten_channel_id", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $extenChannelId;

    /**
     * @ORM\Column(type="bigint", nullable=false, options={"default"=0})
     * @Gedmo\Versioned()
     */
    private $durIvr;

    /**
     * @ORM\Column(type="bigint", nullable=false, options={"default"=0})
     * @Gedmo\Versioned()
     */
    private $durQueue;

    /**
     * @ORM\Column(type="bigint", nullable=false, options={"default"=0})
     * @Gedmo\Versioned()
     */
    private $durExten;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userCall")
     * @Gedmo\Versioned()
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $queueCallId;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $whoCompleted;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * @Gedmo\Versioned()
     */
    private $uid;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * @Gedmo\Versioned()
     */
    private $uid2;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RecordListenLog", mappedBy="record")
     */
    private $recordListenLogs;

    public function __construct()
    {
        $this->recordListenLogs = new ArrayCollection();
    }

    public function getIdx(): ?int
    {
        return $this->idx;
    }

    public function getCallType(): ?string
    {
        return $this->callType;
    }

    public function setCallType(?string $callType): self
    {
        $this->callType = $callType;

        return $this;
    }

    public function getCallId(): ?string
    {
        return $this->callId;
    }

    public function setCallId(string $callId): self
    {
        $this->callId = $callId;

        return $this;
    }

    public function getDt(): ?\DateTimeInterface
    {
        return $this->dt;
    }

    public function setDt(\DateTimeInterface $dt): self
    {
        $this->dt = $dt;

        return $this;
    }

    public function getDid(): ?string
    {
        return $this->did;
    }

    public function setDid(?string $did): self
    {
        $this->did = $did;

        return $this;
    }

    public function getClid(): ?string
    {
        return $this->clid;
    }

    public function setClid(?string $clid): self
    {
        $this->clid = $clid;

        return $this;
    }

    public function getChannelId(): ?string
    {
        return $this->channelId;
    }

    public function setChannelId(?string $channelId): self
    {
        $this->channelId = $channelId;

        return $this;
    }

    public function getDtQueue(): ?\DateTimeInterface
    {
        return $this->dtQueue;
    }

    public function setDtQueue(?\DateTimeInterface $dtQueue): self
    {
        $this->dtQueue = $dtQueue;

        return $this;
    }

    public function getQueue(): ?string
    {
        return $this->queue;
    }

    public function setQueue(?string $queue): self
    {
        $this->queue = $queue;

        return $this;
    }

    public function getDtExten(): ?\DateTimeInterface
    {
        return $this->dtExten;
    }

    public function setDtExten(?\DateTimeInterface $dtExten): self
    {
        $this->dtExten = $dtExten;

        return $this;
    }

    public function getExten(): ?string
    {
        return $this->exten;
    }

    public function setExten(?string $exten): self
    {
        $this->exten = $exten;

        return $this;
    }

    public function getDtHangup(): ?\DateTimeInterface
    {
        return $this->dtHangup;
    }

    public function setDtHangup(?\DateTimeInterface $dtHangup): self
    {
        $this->dtHangup = $dtHangup;

        return $this;
    }

    public function getCallStatus(): ?string
    {
        return $this->callStatus;
    }

    public function setCallStatus(string $callStatus): self
    {
        $this->callStatus = $callStatus;

        return $this;
    }

    public function getUserfield(): ?string
    {
        return $this->userfield;
    }

    public function setUserfield(?string $userfield): self
    {
        $this->userfield = $userfield;

        return $this;
    }

    public function getExtenChannelId(): ?string
    {
        return $this->extenChannelId;
    }

    public function setExtenChannelId(?string $extenChannelId): self
    {
        $this->extenChannelId = $extenChannelId;

        return $this;
    }

    public function getDurIvr(): ?int
    {
        return $this->durIvr;
    }

    public function setDurIvr(int $durIvr): self
    {
        $this->durIvr = $durIvr;

        return $this;
    }

    public function getDurQueue(): ?int
    {
        return $this->durQueue;
    }

    public function setDurQueue(int $durQueue): self
    {
        $this->durQueue = $durQueue;

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

    public function getQueueCallId(): ?string
    {
        return $this->queueCallId;
    }

    public function setQueueCallId(?string $queueCallId): self
    {
        $this->queueCallId = $queueCallId;

        return $this;
    }

    public function getWhoCompleted(): ?string
    {
        return $this->whoCompleted;
    }

    public function setWhoCompleted(string $whoCompleted): self
    {
        $this->whoCompleted = $whoCompleted;

        return $this;
    }

    public function getDurExten(): ?int
    {
        return $this->durExten;
    }

    public function setDurExten(int $durExten): self
    {
        $this->durExten = $durExten;

        return $this;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(?string $uid): self
    {
        $this->uid = $uid;

        return $this;
    }

    public function getUid2(): ?string
    {
        return $this->uid2;
    }

    public function setUid2(?string $uid2): self
    {
        $this->uid2 = $uid2;

        return $this;
    }

    /**
     * @return Collection|RecordListenLog[]
     */
    public function getRecordListenLogs(): Collection
    {
        return $this->recordListenLogs;
    }

    public function addRecordListenLog(RecordListenLog $recordListenLog): self
    {
        if (!$this->recordListenLogs->contains($recordListenLog)) {
            $this->recordListenLogs[] = $recordListenLog;
            $recordListenLog->setRecord($this);
        }

        return $this;
    }

    public function removeRecordListenLog(RecordListenLog $recordListenLog): self
    {
        if ($this->recordListenLogs->contains($recordListenLog)) {
            $this->recordListenLogs->removeElement($recordListenLog);
            // set the owning side to null (unless already changed)
            if ($recordListenLog->getRecord() === $this) {
                $recordListenLog->setRecord(null);
            }
        }

        return $this;
    }
}
