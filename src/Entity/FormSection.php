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
 * @ORM\Entity(repositoryClass="App\Repository\FormSectionRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogFormSection")
 */
class FormSection
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FormTemplate", inversedBy="formSections")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $formTemplate;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $title;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned()
     */
    private $minScore;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned()
     */
    private $sort;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned()
     */
    private $maxScore;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FormCategory", mappedBy="formSection", orphanRemoval=true)
     */
    private $formCategories;


    public function __construct()
    {
        $this->formCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFormTemplate(): ?FormTemplate
    {
        return $this->formTemplate;
    }

    public function setFormTemplate(?FormTemplate $formTemplate): self
    {
        $this->formTemplate = $formTemplate;

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

    public function getMinScore(): ?int
    {
        return $this->minScore;
    }

    public function setMinScore(int $minScore): self
    {
        $this->minScore = $minScore;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getMaxScore(): ?int
    {
        return $this->maxScore;
    }

    public function setMaxScore(int $maxScore): self
    {
        $this->maxScore = $maxScore;

        return $this;
    }

    /**
     * @return Collection|FormCategory[]
     */
    public function getFormCategories(): ?Collection
    {
        return $this->formCategories;
    }

    public function addFormCategory(FormCategory $formCategory): self
    {
        if (!$this->formCategories->contains($formCategory)) {
            $this->formCategories[] = $formCategory;
            $formCategory->setFormSection($this);
        }

        return $this;
    }

    public function removeFormCategory(FormCategory $formCategory): self
    {
        if ($this->formCategories->contains($formCategory)) {
            $this->formCategories->removeElement($formCategory);
            // set the owning side to null (unless already changed)
            if ($formCategory->getFormSection() === $this) {
                $formCategory->setFormSection(null);
            }
        }

        return $this;
    }
}
