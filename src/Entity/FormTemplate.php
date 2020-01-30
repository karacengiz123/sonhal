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
 * @ORM\Entity(repositoryClass="App\Repository\FormTemplateRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogFormTemplate")
 */
class FormTemplate
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
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned()
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FormSection", mappedBy="formTemplate", orphanRemoval=true)
     */
    private $formSections;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FormCategory", mappedBy="formTemplate", orphanRemoval=true)
     */
    private $formCategories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EvaluationResetReason", mappedBy="formTemplateId")
     */
    private $resetReasonId;



    public function __toString()
    {
        return $this->getTitle();
    }

    public function __construct()
    {
        $this->formSections = new ArrayCollection();
        $this->formCategories = new ArrayCollection();
        $this->resetReasonId = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|FormSection[]
     */
    public function getFormSections(): Collection
    {
        return $this->formSections;
    }

    public function addFormSection(FormSection $formSection): self
    {
        if (!$this->formSections->contains($formSection)) {
            $this->formSections[] = $formSection;
            $formSection->setFormTemplate($this);
        }

        return $this;
    }

    public function removeFormSection(FormSection $formSection): self
    {
        if ($this->formSections->contains($formSection)) {
            $this->formSections->removeElement($formSection);
            // set the owning side to null (unless already changed)
            if ($formSection->getFormTemplate() === $this) {
                $formSection->setFormTemplate(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FormCategory[]
     */
    public function getFormCategories(): Collection
    {
        return $this->formCategories;
    }

    public function addFormCategory(FormCategory $formCategory): self
    {
        if (!$this->formCategories->contains($formCategory)) {
            $this->formCategories[] = $formCategory;
            $formCategory->setFormTemplate($this);
        }

        return $this;
    }

    public function removeFormCategory(FormCategory $formCategory): self
    {
        if ($this->formCategories->contains($formCategory)) {
            $this->formCategories->removeElement($formCategory);
            // set the owning side to null (unless already changed)
            if ($formCategory->getFormTemplate() === $this) {
                $formCategory->getFormTemplate(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EvaluationResetReason[]
     */
    public function getResetReasonId(): Collection
    {
        return $this->resetReasonId;
    }

    public function addResetReasonId(EvaluationResetReason $resetReasonId): self
    {
        if (!$this->resetReasonId->contains($resetReasonId)) {
            $this->resetReasonId[] = $resetReasonId;
            $resetReasonId->setFormTemplateId($this);
        }

        return $this;
    }

    public function removeResetReasonId(EvaluationResetReason $resetReasonId): self
    {
        if ($this->resetReasonId->contains($resetReasonId)) {
            $this->resetReasonId->removeElement($resetReasonId);
            // set the owning side to null (unless already changed)
            if ($resetReasonId->getFormTemplateId() === $this) {
                $resetReasonId->setFormTemplateId(null);
            }
        }

        return $this;
    }


}
