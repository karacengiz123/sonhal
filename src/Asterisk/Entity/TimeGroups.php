<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TimeGroups
 * @ApiResource()
 * @ORM\Table(name="time_groups",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\TimeGroupsRepository")
 * @Gedmo\Loggable()
 */
class TimeGroups
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
     * @ORM\Column(name="tc_id", type="integer", nullable=false, options={"comment"="time_condition_id"})
     * @Gedmo\Versioned()
     */
    private $tcId = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="tc", type="string", length=50, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $tc = '';

    public function getIdx(): ?int
    {
        return $this->idx;
    }

    public function getTcId(): ?int
    {
        return $this->tcId;
    }

    public function setTcId(int $tcId): self
    {
        $this->tcId = $tcId;

        return $this;
    }

    public function getTc(): ?string
    {
        return $this->tc;
    }

    public function setTc(string $tc): self
    {
        $this->tc = $tc;

        return $this;
    }


}
