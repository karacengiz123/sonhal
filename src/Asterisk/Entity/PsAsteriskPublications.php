<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * PsAsteriskPublications
 *
 * @ORM\Table(name="ps_asterisk_publications",options={"collate"="utf8_general_ci"})
 * @ORM\Entity
 */
class PsAsteriskPublications
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
     * @ORM\Column(name="devicestate_publish", type="string", length=40, nullable=true)
     */
    private $devicestatePublish;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mailboxstate_publish", type="string", length=40, nullable=true)
     */
    private $mailboxstatePublish;

    /**
     * @var string|null
     *
     * @ORM\Column(name="device_state", type="string", length=0, nullable=true)
     */
    private $deviceState;

    /**
     * @var string|null
     *
     * @ORM\Column(name="device_state_filter", type="string", length=256, nullable=true)
     */
    private $deviceStateFilter;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mailbox_state", type="string", length=0, nullable=true)
     */
    private $mailboxState;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mailbox_state_filter", type="string", length=256, nullable=true)
     */
    private $mailboxStateFilter;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getDevicestatePublish(): ?string
    {
        return $this->devicestatePublish;
    }

    public function setDevicestatePublish(?string $devicestatePublish): self
    {
        $this->devicestatePublish = $devicestatePublish;

        return $this;
    }

    public function getMailboxstatePublish(): ?string
    {
        return $this->mailboxstatePublish;
    }

    public function setMailboxstatePublish(?string $mailboxstatePublish): self
    {
        $this->mailboxstatePublish = $mailboxstatePublish;

        return $this;
    }

    public function getDeviceState(): ?string
    {
        return $this->deviceState;
    }

    public function setDeviceState(?string $deviceState): self
    {
        $this->deviceState = $deviceState;

        return $this;
    }

    public function getDeviceStateFilter(): ?string
    {
        return $this->deviceStateFilter;
    }

    public function setDeviceStateFilter(?string $deviceStateFilter): self
    {
        $this->deviceStateFilter = $deviceStateFilter;

        return $this;
    }

    public function getMailboxState(): ?string
    {
        return $this->mailboxState;
    }

    public function setMailboxState(?string $mailboxState): self
    {
        $this->mailboxState = $mailboxState;

        return $this;
    }

    public function getMailboxStateFilter(): ?string
    {
        return $this->mailboxStateFilter;
    }

    public function setMailboxStateFilter(?string $mailboxStateFilter): self
    {
        $this->mailboxStateFilter = $mailboxStateFilter;

        return $this;
    }


}
