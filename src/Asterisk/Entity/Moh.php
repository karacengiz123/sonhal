<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Moh
 * @ApiResource()
 * @ORM\Table(name="moh",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\MohRepository")
 * @Gedmo\Loggable()
 */
class Moh
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
     * @var int
     *
     * @ORM\Column(name="cus_id", type="integer", nullable=false)
     * @Gedmo\Versioned()
     */
    private $cusId = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=50, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $description = '';

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $url = '';

    public function getIdx(): ?int
    {
        return $this->idx;
    }

    public function getCusId(): ?int
    {
        return $this->cusId;
    }

    public function setCusId(int $cusId): self
    {
        $this->cusId = $cusId;

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }


}
