<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Table(options={"collate"="utf8_general_ci"})
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\RegisterLogRepository")
 */
class RegisterLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="registerLogs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

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
    private $LastRegister;

    /**
     * @ORM\Column(type="bigint", options={"default" = 0}, nullable=true)
     */
    private $duration;

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

    public function getLastRegister(): ?\DateTimeInterface
    {
        return $this->LastRegister;
    }

    public function setLastRegister(?\DateTimeInterface $LastRegister): self
    {
        $this->LastRegister = $LastRegister;

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
