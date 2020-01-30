<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogRole")
 */
class Role
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $title;

    public function __toString()
    {
        return $this->title;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
