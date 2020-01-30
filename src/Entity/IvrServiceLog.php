<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ApiResource()
 * @ORM\Table(options={"collate"="utf8_general_ci"}, name="ivr_service_log", indexes={
 *     @ORM\Index(name="i_ivr_service_Log_creates_at", columns={"creates_at"}),
 *     @ORM\Index(name="i_ivr_service_Log_alias", columns={"alias"}),
 *       })
 * @ORM\Entity(repositoryClass="App\Repository\IvrServiceLogRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogIvrServiceLog")
 */
class IvrServiceLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned()
     */
    private $callId;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned()
     */
    private $alias;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned()
     */
    private $input;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned()
     */
    private $request;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned()
     */
    private $response;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned()
     */
    private $createsAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCallId(): ?string
    {
        return $this->callId;
    }

    public function setCallId(?string $callId): self
    {
        $this->callId = $callId;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function getInput(): ?string
    {
        return $this->input;
    }

    public function setInput(?string $input): self
    {
        $this->input = $input;

        return $this;
    }

    public function getRequest(): ?string
    {
        return $this->request;
    }

    public function setRequest(?string $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(?string $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getCreatesAt(): ?\DateTimeInterface
    {
        return $this->createsAt;
    }

    public function setCreatesAt(?\DateTimeInterface $createsAt): self
    {
        $this->createsAt = $createsAt;

        return $this;
    }
}
