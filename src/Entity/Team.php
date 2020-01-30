<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Table(options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Repository\TeamRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogTeam")
 * @ApiResource()
 * @ApiFilter(SearchFilter::class, properties={"manager":"exact"})
 */
class Team
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="teams")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $manager;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned()
     */
    private $breakLimit;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\user", inversedBy="agentTeams")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="teamId")
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="teamsBackup")
     * @Gedmo\Versioned()
     */
    private $managerBackup;

    public function __toString()
    {
       return $this->getName();
    }

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    public function getManager(): ?User
    {
        return $this->manager;
    }

    public function setManager(?User $manager): self
    {
        $this->manager = $manager;

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
     * @return Collection|user[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(user $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(user $user): self
    {
        if ($this->user->contains($user)) {
            $this->user->removeElement($user);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function getManagerBackup(): ?User
    {
        return $this->managerBackup;
    }

    public function setManagerBackup(?User $managerBackup): self
    {
        $this->managerBackup = $managerBackup;

        return $this;
    }
}
