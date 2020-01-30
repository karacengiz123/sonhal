<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Ivr
 * @ORM\Table(name="ivr",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\IvrRepository")
 * @ApiResource()
 * @ApiFilter(OrderFilter::class, properties={"description": "ASC"})
 * @Gedmo\Loggable()
 */
class Ivr
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
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=50, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $description = '';


    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $title = '';

    /**
     * @var int
     *
     * @ORM\Column(name="rec_id", type="integer", nullable=false, options={"comment"="recording id"})
     * @Gedmo\Versioned()
     */
    private $recId = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="mwt", type="integer", nullable=false, options={"default"="10","comment"="max wait time (0: unlimited, second)"})
     * @Gedmo\Versioned()
     */
    private $mwt = '10';

    /**
     * @var bool
     *
     * @ORM\Column(name="direct_dial", type="boolean", nullable=false, options={"comment"="0: disable, 1: enable"})
     * @Gedmo\Versioned()
     */
    private $directDial = '0';

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title ;

        return $this;
    }

    public function getRecId(): ?int
    {
        return $this->recId;
    }

    public function setRecId(int $recId): self
    {
        $this->recId = $recId;

        return $this;
    }

    public function getMwt(): ?int
    {
        return $this->mwt;
    }

    public function setMwt(int $mwt): self
    {
        $this->mwt = $mwt;

        return $this;
    }

    public function getDirectDial(): ?bool
    {
        return $this->directDial;
    }

    public function setDirectDial(bool $directDial): self
    {
        $this->directDial = $directDial;

        return $this;
    }


}
