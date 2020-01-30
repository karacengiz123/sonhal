<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Customers
 * @ApiResource()
 * @ORM\Table(name="customers",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\CustomersRepository")
 * @Gedmo\Loggable()
 */
class Customers
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
     * @var string
     *
     * @ORM\Column(name="ast_id", type="string", length=6, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $astId = '';

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
     * @ORM\Column(name="prefix", type="string", length=3, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $prefix = '';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $description = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=false, options={"default"="1"})
     * @Gedmo\Versioned()
     */
    private $status = '1';

    public function getIdx(): ?int
    {
        return $this->idx;
    }

    public function getAstId(): ?string
    {
        return $this->astId;
    }

    public function setAstId(string $astId): self
    {
        $this->astId = $astId;

        return $this;
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

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

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

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }


}
