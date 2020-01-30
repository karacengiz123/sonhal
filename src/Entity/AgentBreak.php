<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use App\EventListener\AgentBreakListener;


/**
 * @ApiResource()
 * @ORM\Table(options={"collate"="utf8_general_ci"}, name="agent_break", indexes={@ORM\Index(name="i_user_id", columns={"user_id"}),
 *     @ORM\Index(name="i_break_type_id", columns={"break_type_id"}),
 *     @ORM\Index(name="i_start_time", columns={"start_time"}),
 *     @ORM\Index(name="i_end_time", columns={"end_time"}),
 *     @ORM\Index(name="i_duration", columns={"duration"}),
 *     })
 * @ApiFilter(SearchFilter::class, properties={"breakType":"exact","user":"exact","endTime":"exact"})
 * @ORM\Entity(repositoryClass="App\Repository\AgentBreakRepository")
 * @ORM\EntityListeners("App\EventListener\AgentBreakListener")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogAgentBreak")
 */
class AgentBreak
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"read"})
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="agentBreaks")
     * @ORM\JoinColumn(nullable=false)
     * @ApiSubresource()
     * @Gedmo\Versioned()
     */
    private $user;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\BreakType", inversedBy="agentBreaks")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $breakType;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned()
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

    public function getBreakType(): ?BreakType
    {
        return $this->breakType;
    }

    public function setBreakType(?BreakType $breakType): self
    {
        $this->breakType = $breakType;

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
