<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;



/**
 * @ApiResource()
 * @ORM\Table(options={"collate"="utf8_general_ci"}, name="acw_type", indexes={@ORM\Index(name="i_id", columns={"id"}),
 *     @ORM\Index(name="i_name", columns={"name"}),
 *     @ORM\Index(name="i_state", columns={"state"}),
 * })
 * @ORM\Entity(repositoryClass="App\Repository\AcwTypeRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogAcwType")
 */
class AcwType
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
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $role;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AcwLog", mappedBy="acwType")
     */
    private $acwLogs;

    /**
     * @ORM\Column(type="smallint")
     * @Gedmo\Versioned()
     */
    private $state;

    public function __construct()
    {
        $this->acwLogs = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection|AcwLog[]
     */
    public function getAcwLogs(): Collection
    {
        return $this->acwLogs;
    }

    public function addAcwLog(AcwLog $acwLog): self
    {
        if (!$this->acwLogs->contains($acwLog)) {
            $this->acwLogs[] = $acwLog;
            $acwLog->setAcwType($this);
        }

        return $this;
    }

    public function removeAcwLog(AcwLog $acwLog): self
    {
        if ($this->acwLogs->contains($acwLog)) {
            $this->acwLogs->removeElement($acwLog);
            // set the owning side to null (unless already changed)
            if ($acwLog->getAcwType() === $this) {
                $acwLog->setAcwType(null);
            }
        }

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): self
    {
        $this->state = $state;

        return $this;
    }



}
