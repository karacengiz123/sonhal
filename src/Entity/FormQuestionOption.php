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
 * @ORM\Entity(repositoryClass="App\Repository\FormQuestionRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogFormQuestionOption")
 */
class FormQuestionOption
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FormQuestion", inversedBy="formAnswers")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $question;

    /**
     * @ORM\Column(type="text")
     * @Gedmo\Versioned()
     */
    private $title;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned()
     */
    private $score;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EvaluationAnswer", mappedBy="answer")
     */
    private $evaluationAnswers;

    public function __construct()
    {
        $this->evaluationAnswers = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?FormQuestion
    {
        return $this->question;
    }

    public function setQuestion(?FormQuestion $question): self
    {
        $this->question = $question;

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

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

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
            $evaluationAnswer->setAnswer($this);
        }

        return $this;
    }

    public function removeEvaluationAnswer(EvaluationAnswer $evaluationAnswer): self
    {
        if ($this->evaluationAnswers->contains($evaluationAnswer)) {
            $this->evaluationAnswers->removeElement($evaluationAnswer);
            // set the owning side to null (unless already changed)
            if ($evaluationAnswer->getAnswer() === $this) {
                $evaluationAnswer->setAnswer(null);
            }
        }

        return $this;
    }
}
