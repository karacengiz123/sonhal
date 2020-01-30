<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Arama Listesi
 * Takım Lideri -> Personel İlişkiisi
 * @ApiResource()
 * @ORM\Table(options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Repository\FormQuestionRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogFormQuestion")
 */
class FormQuestion
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Gedmo\Versioned()
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="FormQuestionOption", mappedBy="question", orphanRemoval=true)
     * @ORM\OrderBy({"title" = "ASC"})
     */
    private $formQuestionOptions;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FormCategory", mappedBy="formQuestions")
     */
    private $formCategories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EvaluationAnswer", mappedBy="question")
     */
    private $evaluationAnswers;

    public function __toString()
    {
        return $this->getTitle();
    }

    public function __construct()
    {
        $this->formAnswers = new ArrayCollection();
        $this->formCategories = new ArrayCollection();
        $this->formQuestionOptions = new ArrayCollection();
        $this->evaluationAnswers = new ArrayCollection();
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

    /**
     * @return Collection|FormQuestionOption[]
     */
    public function getFormAnswers(): Collection
    {
        return $this->formAnswers;
    }

    public function addFormAnswer(FormQuestionOption $formQuestionOption): self
    {
        if (!$this->formAnswers->contains($formQuestionOption)) {
            $this->formAnswers[] = $formQuestionOption;
            $formQuestionOption->setQuestion($this);
        }

        return $this;
    }

    public function removeFormAnswer(FormQuestionOption $formQuestionOption): self
    {
        if ($this->formAnswers->contains($formQuestionOption)) {
            $this->formAnswers->removeElement($formQuestionOption);
            // set the owning side to null (unless already changed)
            if ($formQuestionOption->getQuestion() === $this) {
                $formQuestionOption->setQuestion(null);
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
            $formCategory->addFormQuestion($this);
        }

        return $this;
    }

    public function removeFormCategory(FormCategory $formCategory): self
    {
        if ($this->formCategories->contains($formCategory)) {
            $this->formCategories->removeElement($formCategory);
            $formCategory->removeFormQuestion($this);
        }

        return $this;
    }

    /**
     * @return Collection|FormQuestionOption[]
     */
    public function getFormQuestionOptions(): Collection
    {
        return $this->formQuestionOptions;
    }

    public function addFormQuestionOption(FormQuestionOption $formQuestionOption): self
    {
        if (!$this->formQuestionOptions->contains($formQuestionOption)) {
            $this->formQuestionOptions[] = $formQuestionOption;
            $formQuestionOption->setQuestion($this);
        }

        return $this;
    }

    public function removeFormQuestionOption(FormQuestionOption $formQuestionOption): self
    {
        if ($this->formQuestionOptions->contains($formQuestionOption)) {
            $this->formQuestionOptions->removeElement($formQuestionOption);
            // set the owning side to null (unless already changed)
            if ($formQuestionOption->getQuestion() === $this) {
                $formQuestionOption->setQuestion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EvaluationAnswer[]
     */
    public function getEvaluationAnswers(): Collection
    {
        return $this->evaluationAnswers;
    }

    public function addEvaluationAnswer(EvaluationAnswer $evaluationAnswer): self
    {
        if (!$this->evaluationAnswers->contains($evaluationAnswer)) {
            $this->evaluationAnswers[] = $evaluationAnswer;
            $evaluationAnswer->setQuestion($this);
        }

        return $this;
    }

    public function removeEvaluationAnswer(EvaluationAnswer $evaluationAnswer): self
    {
        if ($this->evaluationAnswers->contains($evaluationAnswer)) {
            $this->evaluationAnswers->removeElement($evaluationAnswer);
            // set the owning side to null (unless already changed)
            if ($evaluationAnswer->getQuestion() === $this) {
                $evaluationAnswer->setQuestion(null);
            }
        }

        return $this;
    }
}
