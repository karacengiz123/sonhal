<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\WallMessagesRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogWallMessages")
 */
class WallMessages
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
    private $wallmessages;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWallmessages(): ?string
    {
        return $this->wallmessages;
    }

    public function setWallmessages(?string $wallmessages): self
    {
        $this->wallmessages = $wallmessages;

        return $this;
    }
}
