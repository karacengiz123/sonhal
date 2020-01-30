<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * PsSystems
 * @ApiResource()
 * @ORM\Table(name="ps_systems",options={"collate"="utf8_general_ci"})
 * @ORM\Entity
 */
class PsSystems
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=40, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="timer_t1", type="integer", nullable=true)
     */
    private $timerT1;

    /**
     * @var int|null
     *
     * @ORM\Column(name="timer_b", type="integer", nullable=true)
     */
    private $timerB;

    /**
     * @var string|null
     *
     * @ORM\Column(name="compact_headers", type="string", length=0, nullable=true)
     */
    private $compactHeaders;

    /**
     * @var int|null
     *
     * @ORM\Column(name="threadpool_initial_size", type="integer", nullable=true)
     */
    private $threadpoolInitialSize;

    /**
     * @var int|null
     *
     * @ORM\Column(name="threadpool_auto_increment", type="integer", nullable=true)
     */
    private $threadpoolAutoIncrement;

    /**
     * @var int|null
     *
     * @ORM\Column(name="threadpool_idle_timeout", type="integer", nullable=true)
     */
    private $threadpoolIdleTimeout;

    /**
     * @var int|null
     *
     * @ORM\Column(name="threadpool_max_size", type="integer", nullable=true)
     */
    private $threadpoolMaxSize;

    /**
     * @var string|null
     *
     * @ORM\Column(name="disable_tcp_switch", type="string", length=0, nullable=true)
     */
    private $disableTcpSwitch;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTimerT1(): ?int
    {
        return $this->timerT1;
    }

    public function setTimerT1(?int $timerT1): self
    {
        $this->timerT1 = $timerT1;

        return $this;
    }

    public function getTimerB(): ?int
    {
        return $this->timerB;
    }

    public function setTimerB(?int $timerB): self
    {
        $this->timerB = $timerB;

        return $this;
    }

    public function getCompactHeaders(): ?string
    {
        return $this->compactHeaders;
    }

    public function setCompactHeaders(?string $compactHeaders): self
    {
        $this->compactHeaders = $compactHeaders;

        return $this;
    }

    public function getThreadpoolInitialSize(): ?int
    {
        return $this->threadpoolInitialSize;
    }

    public function setThreadpoolInitialSize(?int $threadpoolInitialSize): self
    {
        $this->threadpoolInitialSize = $threadpoolInitialSize;

        return $this;
    }

    public function getThreadpoolAutoIncrement(): ?int
    {
        return $this->threadpoolAutoIncrement;
    }

    public function setThreadpoolAutoIncrement(?int $threadpoolAutoIncrement): self
    {
        $this->threadpoolAutoIncrement = $threadpoolAutoIncrement;

        return $this;
    }

    public function getThreadpoolIdleTimeout(): ?int
    {
        return $this->threadpoolIdleTimeout;
    }

    public function setThreadpoolIdleTimeout(?int $threadpoolIdleTimeout): self
    {
        $this->threadpoolIdleTimeout = $threadpoolIdleTimeout;

        return $this;
    }

    public function getThreadpoolMaxSize(): ?int
    {
        return $this->threadpoolMaxSize;
    }

    public function setThreadpoolMaxSize(?int $threadpoolMaxSize): self
    {
        $this->threadpoolMaxSize = $threadpoolMaxSize;

        return $this;
    }

    public function getDisableTcpSwitch(): ?string
    {
        return $this->disableTcpSwitch;
    }

    public function setDisableTcpSwitch(?string $disableTcpSwitch): self
    {
        $this->disableTcpSwitch = $disableTcpSwitch;

        return $this;
    }


}
