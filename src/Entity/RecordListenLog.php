<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Table(options={"collate"="utf8_general_ci"})
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\RecordListenLogRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogRecordListenLog")
 */
class RecordListenLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="recordListenLogs")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Calls", inversedBy="recordListenLogs")
     * @ORM\JoinColumn(nullable=false , referencedColumnName="idx")
     * @Gedmo\Versioned()
     */
    private $record;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $clientIp;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Versioned()
     */
    private $listenTime;

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

    public function getRecord(): ?Calls
    {
        return $this->record;
    }

    public function setRecord(?Calls $record): self
    {
        $this->record = $record;

        return $this;
    }

    public function getClientIp(): ?string
    {
        return $this->clientIp;
    }

    public function setClientIp(string $clientIp): self
    {
        $this->clientIp = $clientIp;

        return $this;
    }

    public function getListenTime(): ?\DateTimeInterface
    {
        return $this->listenTime;
    }

    public function setListenTime(\DateTimeInterface $listenTime): self
    {
        $this->listenTime = $listenTime;

        return $this;
    }
}
