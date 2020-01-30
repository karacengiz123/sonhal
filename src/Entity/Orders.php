<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ApiResource()
 * @ORM\Table(options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Repository\OrdersRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogOrders")
 */
class Orders
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="leaderOrdes")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $teamLeader;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned()
     */
    private $subType;

    /**
     * @ORM\Column(type="smallint")
     * @Gedmo\Versioned()
     */
    private $startOrStop;

    use DateTrait;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeamLeader(): ?User
    {
        return $this->teamLeader;
    }

    public function setTeamLeader(?User $teamLeader): self
    {
        $this->teamLeader = $teamLeader;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSubType(): ?int
    {
        return $this->subType;
    }

    public function setSubType(int $subType): self
    {
        $this->subType = $subType;

        return $this;
    }

    public function getStartOrStop(): ?int
    {
        return $this->startOrStop;
    }

    public function setStartOrStop(int $startOrStop): self
    {
        $this->startOrStop = $startOrStop;

        return $this;
    }
}
