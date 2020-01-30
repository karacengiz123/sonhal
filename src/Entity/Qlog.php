<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Table(options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Repository\QlogRepository")
 */
class Qlog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $qlogid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $data1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $data2;

    /**
     * @ORM\Column(type="integer")
     */
    private $data3;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $data4;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $data5;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQlogid(): ?int
    {
        return $this->qlogid;
    }

    public function setQlogid(int $qlogid): self
    {
        $this->qlogid = $qlogid;

        return $this;
    }

    public function getData1(): ?string
    {
        return $this->data1;
    }

    public function setData1(string $data1): self
    {
        $this->data1 = $data1;

        return $this;
    }

    public function getData2(): ?string
    {
        return $this->data2;
    }

    public function setData2(string $data2): self
    {
        $this->data2 = $data2;

        return $this;
    }

    public function getData3(): ?int
    {
        return $this->data3;
    }

    public function setData3(int $data3): self
    {
        $this->data3 = $data3;

        return $this;
    }

    public function getData4(): ?string
    {
        return $this->data4;
    }

    public function setData4(string $data4): self
    {
        $this->data4 = $data4;

        return $this;
    }

    public function getData5(): ?string
    {
        return $this->data5;
    }

    public function setData5(string $data5): self
    {
        $this->data5 = $data5;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }
}
