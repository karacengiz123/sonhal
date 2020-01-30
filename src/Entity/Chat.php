<?php

namespace App\Entity;

use     ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ApiResource()
 * @ORM\Table(options={"collate"="utf8_general_ci"},name="chat", indexes={
 *     @ORM\Index(name="i_chat_user_id", columns={"user_id"}),
 *       @ORM\Index(name="i_chat_status", columns={"status"}),
 *     })
 * @ORM\Entity(repositoryClass="App\Repository\ChatRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogChat")
 */
class Chat
{
    use DateTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="chats")
     * @ORM\JoinColumn(nullable=true)
     * @Gedmo\Versioned()
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=11)
     * @Gedmo\Versioned()
     */
    private $tcid;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Versioned()
     */
    private $startTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned()
     */
    private $endTime;

    /**
     * @ORM\Column(type="smallint")
     * @Gedmo\Versioned()
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ChatMessage", mappedBy="chat")
     */
    private $chatMessages;

    /**
     * @ORM\Column(type="text")
     * @Gedmo\Versioned()
     */
    private $citizen;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $activityId;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $postBody;

    public function __construct()
    {
        $this->chatMessages = new ArrayCollection();
    }

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

    public function getTcid(): ?string
    {
        return $this->tcid;
    }

    public function setTcid(string $tcid): self
    {
        $this->tcid = $tcid;

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

    public function setEndTime(\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|ChatMessage[]
     */
    public function getChatMessages(): Collection
    {
        return $this->chatMessages;
    }

    public function addChatMessage(ChatMessage $chatMessage): self
    {
        if (!$this->chatMessages->contains($chatMessage)) {
            $this->chatMessages[] = $chatMessage;
            $chatMessage->setChat($this);
        }

        return $this;
    }

    public function removeChatMessage(ChatMessage $chatMessage): self
    {
        if ($this->chatMessages->contains($chatMessage)) {
            $this->chatMessages->removeElement($chatMessage);
            // set the owning side to null (unless already changed)
            if ($chatMessage->getChat() === $this) {
                $chatMessage->setChat(null);
            }
        }

        return $this;
    }

    public function getCitizen()
    {
        return $this->citizen;
    }

    public function setCitizen($citizen): self
    {
        $this->citizen = $citizen;

        return $this;
    }

    public function getActivityId(): ?string
    {
        return $this->activityId;
    }

    public function setActivityId(?string $activityId): self
    {
        $this->activityId = $activityId;

        return $this;
    }

    public function getPostBody(): ?string
    {
        return $this->postBody;
    }

    public function setPostBody(?string $postBody): self
    {
        $this->postBody = $postBody;

        return $this;
    }
}
