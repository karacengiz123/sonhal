<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Config
 * @ApiResource()
 * @ORM\Table(name="config",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\ConfigRepository")
 * @Gedmo\Loggable()
 */
class Config
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="value", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $value;

    /**
     * @ORM\Column(name="value_01", type="smallint")
     */
    private $valueOne;

    /**
     * @ORM\Column(name="value_02", type="smallint")
     */
    private $valueTwo;

    /**
     * @ORM\Column(name="value_03", type="smallint")
     */
    private $valueTree;

    /**
     * @ORM\Column(name="value_04", type="smallint")
     */
    private $valueFour;

    /**
     * @ORM\Column(type="string", length=255, options={"default"="temsilci"})
     */
    private $valueTrunk = "temsilci";

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getValueOne(): ?int
    {
        return $this->valueOne;
    }

    public function setValueOne(int $valueOne): self
    {
        $this->valueOne = $valueOne;

        return $this;
    }

    public function getValueTwo(): ?int
    {
        return $this->valueTwo;
    }

    public function setValueTwo(int $valueTwo): self
    {
        $this->valueTwo = $valueTwo;

        return $this;
    }

    public function getValueTree(): ?int
    {
        return $this->valueTree;
    }

    public function setValueTree(int $valueTree): self
    {
        $this->valueTree = $valueTree;

        return $this;
    }

    public function getValueFour(): ?int
    {
        return $this->valueFour;
    }

    public function setValueFour(int $valueFour): self
    {
        $this->valueFour = $valueFour;

        return $this;
    }

    public function getValueTrunk(): ?string
    {
        return $this->valueTrunk;
    }

    public function setValueTrunk(string $valueTrunk): self
    {
        $this->valueTrunk = $valueTrunk;

        return $this;
    }


}
