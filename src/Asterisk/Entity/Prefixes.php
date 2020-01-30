<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Prefixes
 * @ApiResource()
 * @ORM\Table(name="prefixes",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\PrefixesRepository")
 * @Gedmo\Loggable()
 */
class Prefixes
{
    /**
     * @var string
     *
     * @ORM\Column(name="prefix", type="string", length=3, nullable=false, options={"fixed"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $prefix = '';

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=2, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $city = '';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $name = '';

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }


}
