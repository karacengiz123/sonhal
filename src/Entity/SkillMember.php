<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Table(options={"collate"="utf8_general_ci"})
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\SkillMemberRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogSkillMember")
 */
class SkillMember
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $queue;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $member;

    /**
     * @ORM\Column(type="string", length=1)
     * @Gedmo\Versioned()
     */
    private $penalty;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQueue(): ?string
    {
        return $this->queue;
    }

    public function setQueue(string $queue): self
    {
        $this->queue = $queue;

        return $this;
    }

    public function getMember(): ?string
    {
        return $this->member;
    }

    public function setMember(string $member): self
    {
        $this->member = $member;

        return $this;
    }

    public function getPenalty(): ?string
    {
        return $this->penalty;
    }

    public function setPenalty(string $penalty): self
    {
        $this->penalty = $penalty;

        return $this;
    }
}
