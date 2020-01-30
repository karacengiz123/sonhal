<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use phpDocumentor\Reflection\Types\Integer;


/**
 * @ApiResource()
 * @ORM\Table(options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Repository\EvaluationAnswerRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogEvaluationAnswer")
 */
class EvaluationAnswer
{
    use DateTrait;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FormQuestion", inversedBy="evaluationAnswers")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $question;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FormQuestionOption", inversedBy="evaluationAnswers")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $answer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Evaluation", inversedBy="evaluationAnswers" ,cascade={"persist"} )
     * @ORM\JoinColumn(nullable=false )
     * @Gedmo\Versioned()
     */
    private $evaluation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="evaluationAnswers")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $evaluative;

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

    public function getAnswer(): ?FormQuestionOption
    {
        return $this->answer;
    }

    public function setAnswer(?FormQuestionOption $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getEvaluation(): ?Evaluation
    {
        return $this->evaluation;
    }

    public function setEvaluation(?Evaluation $evaluation): self
    {
        $this->evaluation = $evaluation;

        return $this;
    }

    public function getEvaluative(): ?User
    {
        return $this->evaluative;
    }

    public function setEvaluative(?User $evaluative): self
    {
        $this->evaluative = $evaluative;

        return $this;
    }

}
