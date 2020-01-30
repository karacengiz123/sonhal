<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;



/**
 * @ApiResource()
 * @ORM\Table(options={"collate"="utf8_general_ci"}, name="acw_log", indexes={@ORM\Index(name="i_user_id", columns={"user_id"}),
 *     @ORM\Index(name="i_acw_type_id", columns={"acw_type_id"}),
 *     @ORM\Index(name="i_start_time", columns={"start_time"}),
 *     @ORM\Index(name="i_end_time", columns={"end_time"}),
 *     @ORM\Index(name="i_duration", columns={"duration"}),
 *     @ORM\Index(name="i_call_id", columns={"call_id"}),
 * })
 * @ORM\Entity(repositoryClass="App\Repository\AcwLogRepository")
 */
class AcwLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="acwLogs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AcwType", inversedBy="acwLogs")
     */
    private $acwType;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endTime;

    /**
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $callId;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAcwType(): ?AcwType
    {
        return $this->acwType;
    }

    public function setAcwType(?AcwType $acwType): self
    {
        $this->acwType = $acwType;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

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
}
