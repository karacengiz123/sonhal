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
 * @ORM\Entity(repositoryClass="App\Repository\EvaluationResetReasonRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogEvaluationReason")
 */
class EvaluationResetReason
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
     * @ORM\OneToMany(targetEntity="App\Entity\Evaluation", mappedBy="resetReason")
     */
    private $evaluations;

    /**
     * @ORM\Column(type="array")
     * @Gedmo\Versioned()
     */
    private $forms = [];


    public function __toString()
    {
       return $this->getName();
    }

    public function __construct()
    {
        $this->evaluations = new ArrayCollection();
        $this->forms=new ArrayCollection();
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

    /**
     * @return Collection|Evaluation[]
     */
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(Evaluation $evaluation): self
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations[] = $evaluation;
            $evaluation->setResetReason($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): self
    {
        if ($this->evaluations->contains($evaluation)) {
            $this->evaluations->removeElement($evaluation);
            // set the owning side to null (unless already changed)
            if ($evaluation->getResetReason() === $this) {
                $evaluation->setResetReason(null);
            }
        }

        return $this;
    }

    public function getForms(): ?array
    {
        return $this->forms;
    }

    public function setForms(array $forms): self
    {
        $this->forms = $forms;

        return $this;
    }


}
