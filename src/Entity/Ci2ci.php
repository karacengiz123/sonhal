<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ApiResource()
 * @ORM\Table(options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Repository\Ci2ciRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogCi2ci")
 */
class Ci2ci
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned()
     */
    private $callId;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned()
     */
    private $channelId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getcallId(): ?string
    {
        return $this->callId;
    }

    public function setcallId(?string $callId): self
    {
        $this->callId = $callId;

        return $this;
    }

    public function getchannelId(): ?string
    {
        return $this->channelId;
    }

    public function setchannelId(?string $channelId): self
    {
        $this->channelId = $channelId;

        return $this;
    }
}
