<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
//use ApiPlatform\Core\Bridge\Elasticsearch\DataProvider\Filter\MatchFilter;
//use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Queues
 * @ORM\Table(name="queues",options={"collate"="utf8_general_ci"}, indexes={@ORM\Index(name="i_queue", columns={"queue"})})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\QueuesRepository")
 * @ApiResource()
 * @ApiFilter(SearchFilter::class, properties={"queue":"exact"})
 * @Gedmo\Loggable()
 */
class Queues
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="idx", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idx;

    /**
     * @var string
     *
     * @ORM\Column(name="cus_id", type="string", length=7, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $cusId = '';

    /**
     * @var string
     * @ORM\Column(name="queue", type="bigint", nullable=false)
     * @Gedmo\Versioned()
     */
    private $queue = '';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $description = '';

    /**
     * @var string
     *
     * @ORM\Column(name="qprefix", type="string", length=10, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $qprefix = '';

    /**
     * @var int
     *
     * @ORM\Column(name="ja", type="integer", nullable=false, options={"comment"="join announcement (recording id)"})
     * @Gedmo\Versioned()
     */
    private $ja = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="je", type="boolean", nullable=false, options={"default"="1","comment"="join empty"})
     * @Gedmo\Versioned()
     */
    private $je = '1';

    /**
     * @var int
     *
     * @ORM\Column(name="moh", type="integer", nullable=false, options={"comment"="music on hold id"})
     * @Gedmo\Versioned()
     */
    private $moh = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="ringing", type="boolean", nullable=false, options={"comment"="0: moh, 1: ringing"})
     * @Gedmo\Versioned()
     */
    private $ringing = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="mwt", type="integer", nullable=false, options={"default"="1800","comment"="max wait time (0: unlimited, second)"})
     * @Gedmo\Versioned()
     */
    private $mwt = '1800';

    /**
     * @var string
     *
     * @ORM\Column(name="strategy", type="string", length=20, nullable=false, options={"default"="rrmemory","fixed"=true,"comment"="ringall, leastrecent, fewestcalls, random, rrmemory, linear, wrandom"})
     * @Gedmo\Versioned()
     */
    private $strategy = 'rrmemory';

    /**
     * @var int
     *
     * @ORM\Column(name="agent_timeout", type="integer", nullable=false, options={"default"="30","comment"="second"})
     * @Gedmo\Versioned()
     */
    private $agentTimeout = '30';

    /**
     * @var int
     *
     * @ORM\Column(name="retry", type="integer", nullable=false, options={"default"="3","comment"="second"})
     * @Gedmo\Versioned()
     */
    private $retry = '3';

    /**
     * @var int|null
     *
     * @ORM\Column(name="wrap_up_time", type="integer", nullable=true, options={"default"="1","comment"="second"})
     * @Gedmo\Versioned()
     */
    private $wrapUpTime = '1';

    /**
     * @var bool
     *
     * @ORM\Column(name="recording", type="boolean", nullable=false, options={"comment"="0: disable, 1: enable"})
     * @Gedmo\Versioned()
     */
    private $recording = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="announce_position", type="boolean", nullable=false, options={"comment"="0: no, 1: yes"})
     * @Gedmo\Versioned()
     */
    private $announcePosition = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="announce_frequency", type="integer", nullable=false, options={"comment"="second"})
     * @Gedmo\Versioned()
     */
    private $announceFrequency = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="target_type", type="boolean", nullable=false, options={"comment"="0: hangup, 1: ivr, 2: queue, 3: exten, 4: tc, 5: announcement"})
     * @Gedmo\Versioned()
     */
    private $targetType = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="target_id", type="bigint", nullable=false)
     * @Gedmo\Versioned()
     */
    private $targetId = '0';

    public function getIdx(): ?int
    {
        return $this->idx;
    }

    public function getCusId(): ?string
    {
        return $this->cusId;
    }

    public function setCusId(string $cusId): self
    {
        $this->cusId = $cusId;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getQprefix(): ?string
    {
        return $this->qprefix;
    }

    public function setQprefix(string $qprefix): self
    {
        $this->qprefix = $qprefix;

        return $this;
    }

    public function getJa(): ?int
    {
        return $this->ja;
    }

    public function setJa(int $ja): self
    {
        $this->ja = $ja;

        return $this;
    }

    public function getJe(): ?bool
    {
        return $this->je;
    }

    public function setJe(bool $je): self
    {
        $this->je = $je;

        return $this;
    }

    public function getMoh(): ?int
    {
        return $this->moh;
    }

    public function setMoh(int $moh): self
    {
        $this->moh = $moh;

        return $this;
    }

    public function getRinging(): ?bool
    {
        return $this->ringing;
    }

    public function setRinging(bool $ringing): self
    {
        $this->ringing = $ringing;

        return $this;
    }

    public function getMwt(): ?int
    {
        return $this->mwt;
    }

    public function setMwt(int $mwt): self
    {
        $this->mwt = $mwt;

        return $this;
    }

    public function getStrategy(): ?string
    {
        return $this->strategy;
    }

    public function setStrategy(string $strategy): self
    {
        $this->strategy = $strategy;

        return $this;
    }

    public function getAgentTimeout(): ?int
    {
        return $this->agentTimeout;
    }

    public function setAgentTimeout(int $agentTimeout): self
    {
        $this->agentTimeout = $agentTimeout;

        return $this;
    }

    public function getRetry(): ?int
    {
        return $this->retry;
    }

    public function setRetry(int $retry): self
    {
        $this->retry = $retry;

        return $this;
    }

    public function getWrapUpTime(): ?int
    {
        return $this->wrapUpTime;
    }

    public function setWrapUpTime(?int $wrapUpTime): self
    {
        $this->wrapUpTime = $wrapUpTime;

        return $this;
    }

    public function getRecording(): ?bool
    {
        return $this->recording;
    }

    public function setRecording(bool $recording): self
    {
        $this->recording = $recording;

        return $this;
    }

    public function getAnnouncePosition(): ?bool
    {
        return $this->announcePosition;
    }

    public function setAnnouncePosition(bool $announcePosition): self
    {
        $this->announcePosition = $announcePosition;

        return $this;
    }

    public function getAnnounceFrequency(): ?int
    {
        return $this->announceFrequency;
    }

    public function setAnnounceFrequency(int $announceFrequency): self
    {
        $this->announceFrequency = $announceFrequency;

        return $this;
    }

    public function getTargetType(): ?bool
    {
        return $this->targetType;
    }

    public function setTargetType(bool $targetType): self
    {
        $this->targetType = $targetType;

        return $this;
    }

    public function getTargetId(): ?int
    {
        return $this->targetId;
    }

    public function setTargetId(int $targetId): self
    {
        $this->targetId = $targetId;

        return $this;
    }

    public function setIdx(int $idx): self
    {
        $this->idx = $idx;

        return $this;
    }


}
