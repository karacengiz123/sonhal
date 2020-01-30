<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ApiResource()
 * Class RealtimeQueueMembers
 * @ORM\Entity(repositoryClass="App\Repository\RealtimeQueueMembersRepository")
 * @ORM\Table(name="realtime_queue_members",options={"collate"="utf8_general_ci"},indexes={@ORM\Index(name="i_uniqueid", columns={"uniqueid"}),
 *     @ORM\Index(name="i_user_id", columns={"user_id"}),
 *     @ORM\Index(name="i_queue_name", columns={"queue_name"}),
 *     @ORM\Index(name="i_membername", columns={"membername"}),
 * })
 * @ORM\Entity
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogRealtimeQueueMembers")
 */
class RealtimeQueueMembers
{
    /**
     * @var string
     *
     * @ORM\Column(name="uniqueid", type="string", length=80, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @Gedmo\Versioned()
     */
    private $uniqueid;

    /**
     * @var string
     *
     * @ORM\Column(name="queue_name", type="string", length=80, nullable=false)
     * @Gedmo\Versioned()
     */
    private $queueName;

    /**
     * @var string
     *
     * @ORM\Column(name="interface", type="string", length=80, nullable=false)
     * @Gedmo\Versioned()
     */
    private $interface;

    /**
     * @var string|null
     *
     * @ORM\Column(name="membername", type="string", length=80, nullable=true)
     * @Gedmo\Versioned()
     */
    private $membername;

    /**
     * @var string|null
     *
     * @ORM\Column(name="state_interface", type="string", length=80, nullable=true)
     * @Gedmo\Versioned()
     */
    private $stateInterface;

    /**
     * @var int|null
     *
     * @ORM\Column(name="penalty", type="integer", nullable=true)
     * @Gedmo\Versioned()
     */
    private $penalty;

    /**
     * @var int|null
     *
     * @ORM\Column(name="paused", type="integer", nullable=true)
     * @Gedmo\Versioned()
     */
    private $paused;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="realtimeQueueMembers")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $user;

    public function getUniqueid(): ?string {
        return $this->uniqueid;
    }

    public function setUniqueid(string $uniqueid): self {
        $this->uniqueid = $uniqueid;

        return $this;
    }

    public function getQueueName(): ?string {
        return $this->queueName;
    }

    public function setQueueName(string $queueName): self {
        $this->queueName = $queueName;

        return $this;
    }

    public function getInterface(): ?string {
        return $this->interface;
    }

    public function setInterface(string $interface): self {
        $this->interface = $interface;

        return $this;
    }

    public function getMembername(): ?string {
        return $this->membername;
    }

    public function setMembername(?string $membername): self {
        $this->membername = $membername;

        return $this;
    }

    public function getStateInterface(): ?string {
        return $this->stateInterface;
    }

    public function setStateInterface(?string $stateInterface): self {
        $this->stateInterface = $stateInterface;

        return $this;
    }

    public function getPenalty(): ?int {
        return $this->penalty;
    }

    public function setPenalty(?int $penalty): self {
        $this->penalty = $penalty;

        return $this;
    }

    public function getPaused(): ?int {
        return $this->paused;
    }

    public function setPaused(?int $paused): self {
        $this->paused = $paused;

        return $this;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): self {
        $this->user = $user;

        return $this;
    }


}
