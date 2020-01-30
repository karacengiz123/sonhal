<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Phonebook
 * @ApiResource()
 * @ORM\Table(name="phonebook",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\PhonebookRepository")
 * @Gedmo\Loggable()
 */
class Phonebook
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
     * @ORM\Column(name="cus_id", type="string", length=7, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $cusId = '';

    /**
     * @var string
     *
     * @ORM\Column(name="clid", type="string", length=64, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $clid = '';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $name = '';

    public function getIdx(): ?int
    {
        return $this->idx;
    }

    public function getCusId(): ?string
    {
        return $this->cusId;
    }

    public function setCusId(string $cusId): self
    {
        $this->cusId = $cusId;

        return $this;
    }

    public function getClid(): ?string
    {
        return $this->clid;
    }

    public function setClid(string $clid): self
    {
        $this->clid = $clid;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }


}
