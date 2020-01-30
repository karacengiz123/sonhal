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
 * @ApiResource()
 * @ORM\Table(options={"collate"="utf8_general_ci"}, name="break_type", indexes={@ORM\Index(name="i_id", columns={"id"}),
 *     @ORM\Index(name="i_name", columns={"name"}),
 * })
 * @ApiFilter(SearchFilter::class, properties={"name":"exact"})
 * @ORM\Entity(repositoryClass="App\Repository\BreakTypeRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogBreakType")
 */
class BreakType
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
    private $addeableRole;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AgentBreak", mappedBy="breakType")
     */
    private $agentBreaks;

    public function __toString()
    {
        return $this->getName();
    }

    public function __construct()
    {
        $this->agentBreaks = new ArrayCollection();
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

    public function getAddeableRole(): ?string
    {
        return $this->addeableRole;
    }

    public function setAddeableRole(string $addeableRole): self
    {
        $this->addeableRole = $addeableRole;

        return $this;
    }

    /**
     * @return Collection|AgentBreak[]
     */
    public function getAgentBreaks(): Collection
    {
        return $this->agentBreaks;
    }

    public function addAgentBreak(AgentBreak $agentBreak): self
    {
        if (!$this->agentBreaks->contains($agentBreak)) {
            $this->agentBreaks[] = $agentBreak;
            $agentBreak->setBreakType($this);
        }

        return $this;
    }

    public function removeAgentBreak(AgentBreak $agentBreak): self
    {
        if ($this->agentBreaks->contains($agentBreak)) {
            $this->agentBreaks->removeElement($agentBreak);
            // set the owning side to null (unless already changed)
            if ($agentBreak->getBreakType() === $this) {
                $agentBreak->setBreakType(null);
            }
        }

        return $this;
    }
}
