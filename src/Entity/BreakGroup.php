<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ApiResource()
 * @ORM\Table(options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Repository\BreakGroupRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogBreakGroup")
 */
class BreakGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     * @Gedmo\Versioned()
     */
    private $maxUser;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="breakGroup")
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     * @Gedmo\Versioned()
     */
    private $breakLimit;

    public function __toString()
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMaxUser(): ?int
    {
        return $this->maxUser;
    }

    public function setMaxUser(int $maxUser): self
    {
        $this->maxUser = $maxUser;

        return $this;
    }

    public function getBreakLimit(): ?int
    {
        return $this->breakLimit;
    }

    public function setBreakLimit(int $breakLimit): self
    {
        $this->breakLimit = $breakLimit;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
            $user->setBreakGroup($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->user->contains($user)) {
            $this->user->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getBreakGroup() === $this) {
                $user->setBreakGroup(null);
            }
        }

        return $this;
    }

}
