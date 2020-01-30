<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(options={"collate"="utf8_general_ci"})
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\SiebelLogRepository")
 */
class SiebelLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $callid;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $response;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $request;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $activityId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $SRId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ContactId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCallid(): ?string
    {
        return $this->callid;
    }

    public function setCallid(?string $callid): self
    {
        $this->callid = $callid;

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

    public function getRequest(): ?string
    {
        return $this->request;
    }

    public function setRequest(?string $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(?\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    public function getActivityId(): ?string
    {
        return $this->activityId;
    }

    public function setActivityId(?string $activityId): self
    {
        $this->activityId = $activityId;

        return $this;
    }

    public function getSRId(): ?string
    {
        return $this->SRId;
    }

    public function setSRId(?string $SRId): self
    {
        $this->SRId = $SRId;

        return $this;
    }

    public function getContactId(): ?string
    {
        return $this->ContactId;
    }

    public function setContactId(?string $ContactId): self
    {
        $this->ContactId = $ContactId;

        return $this;
    }
}
