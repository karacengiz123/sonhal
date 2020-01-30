<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * QueueLog
 * @ApiResource()
 * @ORM\Table(name="queue_log",options={"collate"="utf8_general_ci"}, indexes={@ORM\Index(name="event", columns={"event"}), @ORM\Index(name="queue", columns={"queuename"})})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\QueueLogRepository")
 * @Gedmo\Loggable()
 */
class QueueLog
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Gedmo\Versioned()
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="time", type="string", length=26, nullable=false)
     * @Gedmo\Versioned()
     */
    private $time = '';

    /**
     * @var string
     *
     * @ORM\Column(name="callid", type="string", length=40, nullable=false)
     * @Gedmo\Versioned()
     */
    private $callid = '';

    /**
     * @var string
     *
     * @ORM\Column(name="queuename", type="string", length=20, nullable=false)
     * @Gedmo\Versioned()
     */
    private $queuename = '';

    /**
     * @var string
     *
     * @ORM\Column(name="agent", type="string", length=20, nullable=false)
     * @Gedmo\Versioned()
     */
    private $agent = '';

    /**
     * @var string
     *
     * @ORM\Column(name="event", type="string", length=20, nullable=false)
     * @Gedmo\Versioned()
     */
    private $event = '';

    /**
     * @var string
     *
     * @ORM\Column(name="data1", type="string", length=100, nullable=false)
     * @Gedmo\Versioned()
     */
    private $data1 = '';

    /**
     * @var string
     *
     * @ORM\Column(name="data2", type="string", length=100, nullable=false)
     * @Gedmo\Versioned()
     */
    private $data2 = '';

    /**
     * @var string
     *
     * @ORM\Column(name="data3", type="string", length=100, nullable=false)
     * @Gedmo\Versioned()
     */
    private $data3 = '';

    /**
     * @var string
     *
     * @ORM\Column(name="data4", type="string", length=100, nullable=false)
     * @Gedmo\Versioned()
     */
    private $data4 = '';

    /**
     * @var string
     *
     * @ORM\Column(name="data5", type="string", length=100, nullable=false)
     * @Gedmo\Versioned()
     */
    private $data5 = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     * @Gedmo\Versioned()
     */
    private $created = 'CURRENT_TIMESTAMP';


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getTime(): string
    {
        return $this->time;
    }

    /**
     * @param string $time
     */
    public function setTime(string $time): void
    {
        $this->time = $time;
    }


    /**
     * @return string
     */
    public function getCallid(): string
    {
        return $this->callid;
    }

    /**
     * @param string $callid
     */
    public function setCallid(string $callid): void
    {
        $this->callid = $callid;
    }

    /**
     * @return string
     */
    public function getQueuename(): string
    {
        return $this->queuename;
    }

    /**
     * @param string $queuename
     */
    public function setQueuename(string $queuename): void
    {
        $this->queuename = $queuename;
    }

    /**
     * @return string
     */
    public function getAgent(): string
    {
        return $this->agent;
    }

    /**
     * @param string $agent
     */
    public function setAgent(string $agent): void
    {
        $this->agent = $agent;
    }

    /**
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @param string $event
     */
    public function setEvent(string $event): void
    {
        $this->event = $event;
    }

    /**
     * @return string
     */
    public function getData1(): string
    {
        return $this->data1;
    }

    /**
     * @param string $data1
     */
    public function setData1(string $data1): void
    {
        $this->data1 = $data1;
    }

    /**
     * @return string
     */
    public function getData2(): string
    {
        return $this->data2;
    }

    /**
     * @param string $data2
     */
    public function setData2(string $data2): void
    {
        $this->data2 = $data2;
    }

    /**
     * @return string
     */
    public function getData3(): string
    {
        return $this->data3;
    }

    /**
     * @param string $data3
     */
    public function setData3(string $data3): void
    {
        $this->data3 = $data3;
    }

    /**
     * @return string
     */
    public function getData4(): string
    {
        return $this->data4;
    }

    /**
     * @param string $data4
     */
    public function setData4(string $data4): void
    {
        $this->data4 = $data4;
    }

    /**
     * @return string
     */
    public function getData5(): string
    {
        return $this->data5;
    }

    /**
     * @param string $data5
     */
    public function setData5(string $data5): void
    {
        $this->data5 = $data5;
    }

    /**
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated(\DateTime $created): void
    {
        $this->created = $created;
    }
}
