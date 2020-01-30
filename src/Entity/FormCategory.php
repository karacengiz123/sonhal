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
 * @ORM\Entity(repositoryClass="App\Repository\FormCategoryRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogFormCategory")
 */
class FormCategory
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
     * @ORM\Column(type="text")
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
     * @ORM\ManyToOne(targetEntity="App\Entity\FormSection", inversedBy="formCategories")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $formSection;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FormQuestion", inversedBy="formCategories")
     */
    private $formQuestions;

    public function __construct()
    {
        $this->formQuestions = new ArrayCollection();
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

    public function getFormSection(): ?FormSection
    {
        return $this->formSection;
    }

    public function setFormSection(?FormSection $formSection): self
    {
        $this->formSection = $formSection;

        return $this;
    }

    /**
     * @return Collection|FormQuestion[]
     */
    public function getFormQuestions(): Collection
    {
        return $this->formQuestions;
    }

    public function addFormQuestion(FormQuestion $formQuestion): self
    {
        if (!$this->formQuestions->contains($formQuestion)) {
            $this->formQuestions[] = $formQuestion;
        }

        return $this;
    }

    public function removeFormQuestion(FormQuestion $formQuestion): self
    {
        if ($this->formQuestions->contains($formQuestion)) {
            $this->formQuestions->removeElement($formQuestion);
        }

        return $this;
    }


}
