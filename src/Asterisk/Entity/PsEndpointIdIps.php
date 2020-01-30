<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * PsEndpointIdIps
 * @ApiResource()
 * @ORM\Table(name="ps_endpoint_id_ips",options={"collate"="utf8_general_ci"})
 * @ORM\Entity
 */
class PsEndpointIdIps
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
     * @ORM\Column(name="match", type="string", length=80, nullable=true)
     */
    private $match;

    /**
     * @var string|null
     *
     * @ORM\Column(name="srv_lookups", type="string", length=0, nullable=true)
     */
    private $srvLookups;

    /**
     * @var string|null
     *
     * @ORM\Column(name="match_header", type="string", length=255, nullable=true)
     */
    private $matchHeader;

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

    public function getMatch(): ?string
    {
        return $this->match;
    }

    public function setMatch(?string $match): self
    {
        $this->match = $match;

        return $this;
    }

    public function getSrvLookups(): ?string
    {
        return $this->srvLookups;
    }

    public function setSrvLookups(?string $srvLookups): self
    {
        $this->srvLookups = $srvLookups;

        return $this;
    }

    public function getMatchHeader(): ?string
    {
        return $this->matchHeader;
    }

    public function setMatchHeader(?string $matchHeader): self
    {
        $this->matchHeader = $matchHeader;

        return $this;
    }


}
