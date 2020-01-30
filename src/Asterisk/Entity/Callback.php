<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Callback
 * @ApiResource()
 * @ORM\Table(name="callback",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\CallbackRepository")
 * @Gedmo\Loggable()
 */
class Callback
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
     * @var int
     *
     * @ORM\Column(name="did", type="integer", nullable=false, options={"comment"="recording id"})
     * @Gedmo\Versioned()
     */
    private $did = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=50, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $description = '';

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

    public function getCusId(): ?int
    {
        return $this->cusId;
    }

    public function setCusId(int $cusId): self
    {
        $this->cusId = $cusId;

        return $this;
    }

    public function getDid(): ?int
    {
        return $this->did;
    }

    public function setDid(int $did): self
    {
        $this->did = $did;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
