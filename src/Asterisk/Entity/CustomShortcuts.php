<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * CustomShortcuts
 * @ApiResource()
 * @ORM\Table(name="custom_shortcuts",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\CustomShortcutsRepository")
 * @Gedmo\Loggable()
 */
class CustomShortcuts
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
     * @var string
     *
     * @ORM\Column(name="cus_id", type="string", length=7, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $cusId = '';

    /**
     * @var string
     *
     * @ORM\Column(name="scode", type="string", length=4, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $scode = '';

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

    public function getCusId(): ?string
    {
        return $this->cusId;
    }

    public function setCusId(string $cusId): self
    {
        $this->cusId = $cusId;

        return $this;
    }

    public function getScode(): ?string
    {
        return $this->scode;
    }

    public function setScode(string $scode): self
    {
        $this->scode = $scode;

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
