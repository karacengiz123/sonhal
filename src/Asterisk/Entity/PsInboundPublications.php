<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * PsInboundPublications
 * @ApiResource()
 * @ORM\Table(name="ps_inbound_publications",options={"collate"="utf8_general_ci"})
 * @ORM\Entity
 */
class PsInboundPublications
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
     * @ORM\Column(name="endpoint", type="string", length=40, nullable=true)
     */
    private $endpoint;

    /**
     * @var string|null
     *
     * @ORM\Column(name="`event_asterisk-devicestate`", type="string", length=40, nullable=true)
     */
    private $eventAsteriskDevicestate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="`event_asterisk-mwi`", type="string", length=40, nullable=true)
     */
    private $eventAsteriskMwi;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }

    public function setEndpoint(?string $endpoint): self
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function getEventAsteriskDevicestate(): ?string
    {
        return $this->eventAsteriskDevicestate;
    }

    public function setEventAsteriskDevicestate(?string $eventAsteriskDevicestate): self
    {
        $this->eventAsteriskDevicestate = $eventAsteriskDevicestate;

        return $this;
    }

    public function getEventAsteriskMwi(): ?string
    {
        return $this->eventAsteriskMwi;
    }

    public function setEventAsteriskMwi(?string $eventAsteriskMwi): self
    {
        $this->eventAsteriskMwi = $eventAsteriskMwi;

        return $this;
    }


}
