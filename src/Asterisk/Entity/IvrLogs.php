<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * IvrLogs
 * @ORM\Table(name="ivr_logs",options={"collate"="utf8_general_ci"}, indexes={@ORM\Index(name="i_call_id", columns={"call_id"})})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\IvrLogsRepository")
 * @ApiResource()
 * @ApiFilter(SearchFilter::class, properties={"callId":"exact"})
 * @Gedmo\Loggable()
 */
class IvrLogs
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
     * @var \DateTime
     *
     * @ORM\Column(name="dt", type="datetime", nullable=true)
     * @Gedmo\Versioned()
     */
    private $dt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="call_id", type="string", length=80, nullable=true, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $callId;

    /**
     * @var int
     *
     * @ORM\Column(name="ivr_id", type="integer", nullable=false, options={"comment"="ivr_id"})
     * @Gedmo\Versioned()
     */
    private $ivrId = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="choice", type="string", length=3, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $choice = '';


    /**
     * @return int
     */
    public function getIdx(): int
    {
        return $this->idx;
    }

    /**
     * @return \DateTime
     */
    public function getDt(): \DateTime
    {
        return $this->dt;
    }

    /**
     * @param \DateTime $dt
     */
    public function setDt(\DateTime $dt): void
    {
        $this->dt = $dt;
    }

    /**
     * @return string|null
     */
    public function getCallId(): ?string
    {
        return $this->callId;
    }

    /**
     * @param string|null $callId
     */
    public function setCallId(?string $callId): void
    {
        $this->callId = $callId;
    }

    /**
     * @return int
     */
    public function getIvrId(): int
    {
        return $this->ivrId;
    }

    /**
     * @param int $ivrId
     */
    public function setIvrId(int $ivrId): void
    {
        $this->ivrId = $ivrId;
    }

    /**
     * @return string
     */
    public function getChoice(): string
    {
        return $this->choice;
    }

    /**
     * @param string $choice
     */
    public function setChoice(string $choice): void
    {
        $this->choice = $choice;
    }

}
