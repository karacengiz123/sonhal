<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ApiResource()
 * @ORM\Table(options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Repository\QuestionsRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogQuestions")
 */
class Questions
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned()
     */
    private $template_id;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned()
     */
    private $question_id;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned()
     */
    private $question_option_id;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned()
     */
    private $score;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTemplateId(): ?int
    {
        return $this->template_id;
    }

    public function setTemplateId(int $template_id): self
    {
        $this->template_id = $template_id;

        return $this;
    }

    public function getQuestionId(): ?int
    {
        return $this->question_id;
    }

    public function setQuestionId(int $question_id): self
    {
        $this->question_id = $question_id;

        return $this;
    }

    public function getQuestionOptionId(): ?int
    {
        return $this->question_option_id;
    }

    public function setQuestionOptionId(int $question_option_id): self
    {
        $this->question_option_id = $question_option_id;

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
}
