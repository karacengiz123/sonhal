<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ApiResource()
 * @ORM\Table(options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Repository\AppChatMessagesRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogAppChatMessages")
 */
class AppChatMessages
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AcwLog", inversedBy="appChatMessages")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $chat;

    /**
     * @ORM\Column(type="smallint")
     * @Gedmo\Versioned()
     */
    private $sender;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $message;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChat(): ?AcwLog
    {
        return $this->chat;
    }

    public function setChat(?AcwLog $chat): self
    {
        $this->chat = $chat;

        return $this;
    }

    public function getSender(): ?int
    {
        return $this->sender;
    }

    public function setSender(int $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
