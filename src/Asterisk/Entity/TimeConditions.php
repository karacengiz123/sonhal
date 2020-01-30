<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TimeConditions
 * @ApiResource()
 * @ORM\Table(name="time_conditions",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\TimeConditionsRepository")
 * @Gedmo\Loggable()
 */
class TimeConditions
{
    /**
     * @var int
     *
     * @ORM\Column(name="idx", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idx;

    /**
     * @var int
     *
     * @ORM\Column(name="cus_id", type="integer", nullable=false)
     * @Gedmo\Versioned()
     */
    private $cusId = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="true_target_type", type="boolean", nullable=false, options={"comment"="0: hangup, 1: ivr, 2: queue, 3: exten, 4: tc, 5: announcement"})
     * @Gedmo\Versioned()
     */
    private $trueTargetType = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="true_target_id", type="bigint", nullable=false)
     * @Gedmo\Versioned()
     */
    private $trueTargetId = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="false_target_type", type="boolean", nullable=false, options={"comment"="0: hangup, 1: ivr, 2: queue, 3: exten, 4: tc, 5: announcement"})
     * @Gedmo\Versioned()
     */
    private $falseTargetType = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="false_target_id", type="bigint", nullable=false)
     * @Gedmo\Versioned()
     */
    private $falseTargetId = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=250, nullable=true)
     * @Gedmo\Versioned()
     */
    private $description;

    public function getIdx(): ?int
    {
        return $this->idx;
    }

    public function getCusId(): ?int
    {
        return $this->cusId;
    }

    public function setCusId(int $cusId): self
    {
        $this->cusId = $cusId;

        return $this;
    }

    public function getTrueTargetType(): ?bool
    {
        return $this->trueTargetType;
    }

    public function setTrueTargetType(bool $trueTargetType): self
    {
        $this->trueTargetType = $trueTargetType;

        return $this;
    }

    public function getTrueTargetId(): ?int
    {
        return $this->trueTargetId;
    }

    public function setTrueTargetId(int $trueTargetId): self
    {
        $this->trueTargetId = $trueTargetId;

        return $this;
    }

    public function getFalseTargetType(): ?bool
    {
        return $this->falseTargetType;
    }

    public function setFalseTargetType(bool $falseTargetType): self
    {
        $this->falseTargetType = $falseTargetType;

        return $this;
    }

    public function getFalseTargetId(): ?int
    {
        return $this->falseTargetId;
    }

    public function setFalseTargetId(int $falseTargetId): self
    {
        $this->falseTargetId = $falseTargetId;

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
