<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use phpDocumentor\Reflection\Types\Integer;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

/**
 * @ApiResource()
 * @ORM\Table(
 *    options={"collate"="utf8_general_ci"},
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(
 *            columns={"user_id" , "source_dest_id","deletedAt"})
 *    }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\EvaluationRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogEvaluation")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt" ,timeAware=false)
 */
class Evaluation
{
    use DateTrait;


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FormTemplate")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $formTemplate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="evaluations")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $user;


    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $sourceDestID; // CallID, siebel Başvuru ID , Igdaş CRM ID , Sosyal abuz

    /**
     * @ORM\Column(type="float")
     * @Gedmo\Versioned()
     */
    private $score;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned()
     */
    private $comment;


    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     * @Gedmo\Versioned()
     */
    private $deletedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="byevaluations")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $evaluative;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EvaluationAnswer", mappedBy="evaluation" ,cascade={"persist"} )
     */
    private $evaluationAnswers;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EvaluationSource", inversedBy="evaluations")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $source;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EvaluationResetReason", inversedBy="evaluations")
     * @Gedmo\Versioned()
     */
    private $resetReason;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EvaluationStatus", inversedBy="evaluations")
     * @ORM\JoinColumn(nullable=true)
     * @Gedmo\Versioned()
     */
    private $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned()
     */
    private $evaluatorComment;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned()
     */
    private $evaluativeComment;

    /**"
     * @ORM\OneToMany(targetEntity="App\Entity\EvaluationExtraSource", mappedBy="evaluation")
     */
    private $evaluationExtraSources;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     * @Gedmo\Versioned()
     */
    private $duration;

    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     * @Gedmo\Versioned()
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned()
     */
    private $callDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EvaluationReasonType", inversedBy="evaluations")
     * @Gedmo\Versioned()
     */
    private $evaluationReasonType;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned()
     */
    private $rejectComment;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned()
     */
    private $rejectCloseComment;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $citizenID;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EvaluationType", inversedBy="evaluations")
     */
    private $evaluationType;



    public function __construct() {
        $this->evaluationAnswers = new ArrayCollection();
        $this->evaluationExtraSources = new ArrayCollection();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(float $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getSourceDestID(): ?string
    {
        return $this->sourceDestID;
    }

    public function setSourceDestID(string $sourceDestID): self
    {
        $this->sourceDestID = $sourceDestID;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

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
            $evaluationAnswer->setEvaluation($this);
        }

        return $this;
    }

    public function removeEvaluationAnswer(EvaluationAnswer $evaluationAnswer): self
    {
        if ($this->evaluationAnswers->contains($evaluationAnswer)) {
            $this->evaluationAnswers->removeElement($evaluationAnswer);
            // set the owning side to null (unless already changed)
            if ($evaluationAnswer->getEvaluation() === $this) {
                $evaluationAnswer->setEvaluation(null);
            }
        }

        return $this;
    }

    public function getSource(): ?EvaluationSource
    {
        return $this->source;
    }

    public function setSource(?EvaluationSource $source): self
    {
        $this->source = $source;

        return $this;
    }



    public function getResetReason(): ?EvaluationResetReason
    {
        return $this->resetReason;
    }

    public function setResetReason(?EvaluationResetReason $resetReason): self
    {
        $this->resetReason = $resetReason;

        return $this;
    }

    public function getStatus(): ?EvaluationStatus
    {
        return $this->status;
    }

    public function setStatus(?EvaluationStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getEvaluatorComment(): ?string
    {
        return $this->evaluatorComment;
    }

    public function setEvaluatorComment(?string $evaluatorComment): self
    {
        $this->evaluatorComment = $evaluatorComment;

        return $this;
    }

    public function getEvaluativeComment(): ?string
    {
        return $this->evaluativeComment;
    }

    public function setEvaluativeComment(?string $evaluativeComment): self
    {
        $this->evaluativeComment = $evaluativeComment;

        return $this;
    }

    /**
     * @return Collection|EvaluationExtraSource[]
     */
    public function getEvaluationExtraSources(): Collection
    {
        return $this->evaluationExtraSources;
    }

    public function addEvaluationExtraSource(EvaluationExtraSource $evaluationExtraSource): self
    {
        if (!$this->evaluationExtraSources->contains($evaluationExtraSource)) {
            $this->evaluationExtraSources[] = $evaluationExtraSource;
            $evaluationExtraSource->setEvaluation($this);
        }

        return $this;
    }

    public function removeEvaluationExtraSource(EvaluationExtraSource $evaluationExtraSource): self
    {
        if ($this->evaluationExtraSources->contains($evaluationExtraSource)) {
            $this->evaluationExtraSources->removeElement($evaluationExtraSource);
            // set the owning side to null (unless already changed)
            if ($evaluationExtraSource->getEvaluation() === $this) {
                $evaluationExtraSource->setEvaluation(null);
            }
        }

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getCallDate(): ?\DateTimeInterface
    {
        return $this->callDate;
    }

    public function setCallDate(\DateTimeInterface $callDate): self
    {
        $this->callDate = $callDate;

        return $this;
    }

    public function getEvaluationReasonType(): ?EvaluationReasonType
    {
        return $this->evaluationReasonType;
    }

    public function setEvaluationReasonType(?EvaluationReasonType $evaluationReasonType): self
    {
        $this->evaluationReasonType = $evaluationReasonType;

        return $this;
    }

    public function getRejectComment(): ?string
    {
        return $this->rejectComment;
    }

    public function setRejectComment(?string $rejectComment): self
    {
        $this->rejectComment = $rejectComment;

        return $this;
    }

    public function getRejectCloseComment(): ?string
    {
        return $this->rejectCloseComment;
    }

    public function setRejectCloseComment(?string $rejectCloseComment): self
    {
        $this->rejectCloseComment = $rejectCloseComment;

        return $this;
    }

    public function getCitizenID(): ?string
    {
        return $this->citizenID;
    }

    public function setCitizenID(string $citizenID): self
    {
        $this->citizenID = $citizenID;

        return $this;
    }

    public function getEvaluationType(): ?EvaluationType
    {
        return $this->evaluationType;
    }

    public function setEvaluationType(?EvaluationType $evaluationType): self
    {
        $this->evaluationType = $evaluationType;

        return $this;
    }





}
