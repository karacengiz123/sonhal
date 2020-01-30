<?php

namespace App\Entity\Session;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Sessions
 *
 * @ORM\Table(name="sessions")
 * @ORM\Entity(repositoryClass="App\Repository\Session\SessionRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\Session\LogSession")
 *
 */
class Sessions
{
    /**
     * @var string
     *
     * @ORM\Column(name="sess_id", type="string", length=128, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $sessId;

    /**
     * @var string
     *
     * @ORM\Column(name="sess_data", type="blob", length=65535, nullable=false)
     */
    private $sessData;

    /**
     * @var int
     *
     * @ORM\Column(name="sess_time", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $sessTime;

    /**
     * @var int
     *
     * @ORM\Column(name="sess_lifetime", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $sessLifetime;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="bigint", nullable=true, options={"default"="NULL","unsigned"=true})
     */
    private $id = 'NULL';

    public function getSessId(): ?string
    {
        return $this->sessId;
    }

    public function getSessData()
    {
        return $this->sessData;
    }

    public function setSessData($sessData): self
    {
        $this->sessData = $sessData;

        return $this;
    }

    public function getSessTime(): ?string
    {
        return $this->sessTime;
    }

    public function setSessTime(string $sessTime): self
    {
        $this->sessTime = $sessTime;

        return $this;
    }

    public function getSessLifetime(): ?string
    {
        return $this->sessLifetime;
    }

    public function setSessLifetime(string $sessLifetime): self
    {
        $this->sessLifetime = $sessLifetime;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }


}
