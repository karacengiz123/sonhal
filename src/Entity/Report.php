<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ApiResource()
 * @ORM\Table(options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Repository\ReportRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogReport")
 */
class Report
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned()
     */
    private $firstdate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned()
     */
    private $lastdate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstdate(): ?\DateTimeInterface
    {
        return $this->firstdate;
    }

    public function setFirstdate(?\DateTimeInterface $firstdate): self
    {
        $this->firstdate = $firstdate;

        return $this;
    }

    public function getLastdate(): ?\DateTimeInterface
    {
        return $this->lastdate;
    }

    public function setLastdate(?\DateTimeInterface $lastdate): self
    {
        $this->lastdate = $lastdate;

        return $this;
    }
}
