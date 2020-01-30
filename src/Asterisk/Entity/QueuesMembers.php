<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * QueuesMembers
 *
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\QueuesMembersRepository")
 * @ORM\Table(name="queues_members",options={"collate"="utf8_general_ci"}, uniqueConstraints={
 *      @ORM\UniqueConstraint(name="quee_member", columns={"queue", "member"})
 * })
 * @ApiResource()
 * @Gedmo\Loggable()
 */
class QueuesMembers
{
    /**
     * @var int
     *
     * @ORM\Column(name="idx", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idx;

    /**
     * @var string $queue
     *
     * @ORM\Column(name="queue", type="string", length=9, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $queue = '';

    /**
     * @var string $member
     *
     * @ORM\Column(name="member", type="string", length=10, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $member = '';

    /**
     * @var string
     *
     * @ORM\Column(name="penalty", type="string", length=1, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $penalty = '0';

    public function getIdx(): ?int
    {
        return $this->idx;
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
