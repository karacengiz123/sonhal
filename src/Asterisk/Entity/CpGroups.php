<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * CpGroups
 * @ApiResource()
 * @ORM\Table(name="cp_groups",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\CpGroupsRepository")
 * @Gedmo\Loggable()
 */
class CpGroups
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
     * @ORM\Column(name="cp_group", type="string", length=10, nullable=false, options={"fixed"=true,"comment"="0-63"})
     * @Gedmo\Versioned()
     */
    private $cpGroup = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $description = '';

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

    public function getCpGroup(): ?string
    {
        return $this->cpGroup;
    }

    public function setCpGroup(string $cpGroup): self
    {
        $this->cpGroup = $cpGroup;

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


}
