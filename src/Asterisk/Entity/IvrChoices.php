<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * IvrChoices
 * @ApiResource()
 * @ORM\Table(name="ivr_choices",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\IvrChoicesRepository")
 * @Gedmo\Loggable()
 */
class IvrChoices
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
     * @ORM\Column(name="ivr_id", type="integer", nullable=false, options={"comment"="ivr_id"})
     * @Gedmo\Versioned()
     */
    private $ivrId = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="choice", type="string", length=3, nullable=false, options={"default"="i","fixed"=true,"comment"="0-9, *,"})
     * @Gedmo\Versioned()
     */
    private $choice = 'i';

    /**
     * @var bool
     *
     * @ORM\Column(name="target_type", type="boolean", nullable=false, options={"comment"="0: hangup, 1: ivr, 2: queue, 3: exten, 4: tc, 5: announcement"})
     * @Gedmo\Versioned()
     */
    private $targetType = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="target_id", type="bigint", nullable=false)
     * @Gedmo\Versioned()
     */
    private $targetId = '0';

    public function getIdx(): ?int
    {
        return $this->idx;
    }

    public function getIvrId(): ?int
    {
        return $this->ivrId;
    }

    public function setIvrId(int $ivrId): self
    {
        $this->ivrId = $ivrId;

        return $this;
    }

    public function getChoice(): ?string
    {
        return $this->choice;
    }

    public function setChoice(string $choice): self
    {
        $this->choice = $choice;

        return $this;
    }

    public function getTargetType(): ?bool
    {
        return $this->targetType;
    }

    public function setTargetType(bool $targetType): self
    {
        $this->targetType = $targetType;

        return $this;
    }

    public function getTargetId(): ?int
    {
        return $this->targetId;
    }

    public function setTargetId(int $targetId): self
    {
        $this->targetId = $targetId;

        return $this;
    }


}
