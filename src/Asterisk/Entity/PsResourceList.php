<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * PsResourceList
 * @ApiResource()
 * @ORM\Table(name="ps_resource_list",options={"collate"="utf8_general_ci"})
 * @ORM\Entity
 */
class PsResourceList
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
     * @var string|null
     *
     * @ORM\Column(name="list_item", type="string", length=2048, nullable=true)
     */
    private $listItem;

    /**
     * @var string|null
     *
     * @ORM\Column(name="event", type="string", length=40, nullable=true)
     */
    private $event;

    /**
     * @var string|null
     *
     * @ORM\Column(name="full_state", type="string", length=0, nullable=true)
     */
    private $fullState;

    /**
     * @var int|null
     *
     * @ORM\Column(name="notification_batch_interval", type="integer", nullable=true)
     */
    private $notificationBatchInterval;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getListItem(): ?string
    {
        return $this->listItem;
    }

    public function setListItem(?string $listItem): self
    {
        $this->listItem = $listItem;

        return $this;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function setEvent(?string $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getFullState(): ?string
    {
        return $this->fullState;
    }

    public function setFullState(?string $fullState): self
    {
        $this->fullState = $fullState;

        return $this;
    }

    public function getNotificationBatchInterval(): ?int
    {
        return $this->notificationBatchInterval;
    }

    public function setNotificationBatchInterval(?int $notificationBatchInterval): self
    {
        $this->notificationBatchInterval = $notificationBatchInterval;

        return $this;
    }


}
