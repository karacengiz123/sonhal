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
 * @ORM\Entity(repositoryClass="App\Repository\EvaluationExtraSourceRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogEvaluationExtraSource")
 */
class EvaluationExtraSource
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
    private $source;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Evaluation", inversedBy="evaluationExtraSources")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $evaluation;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned()
     */
    private $description;

    public function __toString()
    {
        return $this->source;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

}
