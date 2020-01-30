<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class QueuesMembersDynamic
 * @ORM\Table("queues_members_dynamic",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Repository\QueuesMembersDynamicRepository")
 * @ApiResource()
 * @ApiFilter(SearchFilter::class, properties={"queue":"exact","member":"exact","penalty":"exact"})
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogQueuesMembersDynamic")
 */
class QueuesMembersDynamic
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Gedmo\Versioned()
     */
    private $queue;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Gedmo\Versioned()
     */
    private $member;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
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
