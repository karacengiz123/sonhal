<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ApiResource()
 * @ORM\Table(options={"collate"="utf8_general_ci"}, name="login_log", indexes={
 *     @ORM\Index(name="i_start_time", columns={"start_time"}),
 *     @ORM\Index(name="i_nd_time", columns={"end_time"})
 *     })
 * @ORM\Entity(repositoryClass="App\Repository\LoginLogRepository")

 */
class LoginLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="loginLogs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userId;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $StartTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $EndTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $LastOnline;

    /**
     * @ORM\Column(type="bigint", options={"default" = 0}, nullable=true)
     */
    private $duration;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->StartTime;
    }

    public function setStartTime(?\DateTimeInterface $StartTime): self
    {
        $this->StartTime = $StartTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->EndTime;
    }

    public function setEndTime(?\DateTimeInterface $EndTime): self
    {
        $this->EndTime = $EndTime;

        return $this;
    }

    public function getLastOnline(): ?\DateTimeInterface
    {
        return $this->LastOnline;
    }

    public function setLastOnline(?\DateTimeInterface $LastOnline): self
    {
        $this->LastOnline = $LastOnline;

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
}
