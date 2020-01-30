<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Cities
 * @ApiResource()
 * @ORM\Table(name="cities",options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\CitiesRepository")
 * @Gedmo\Loggable()
 */
class Cities
{
    /**
     * @var string
     *
     * @ORM\Column(name="citystr", type="string", length=255, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $citystr;

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
     * @ORM\Column(name="prefix", type="string", length=3, nullable=false, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $prefix = '';

    public function getCitystr(): ?string
    {
        return $this->citystr;
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

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }


}
