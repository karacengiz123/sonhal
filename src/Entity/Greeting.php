<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * This is a dummy entity. Remove it!
 * @ORM\Table(options={"collate"="utf8_general_ci"})
 * @ApiResource
 * @ORM\Entity
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogGreeting")
 */
class Greeting
{
    /**
     * @var int The entity Id
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string A nice person
     * @Gedmo\Versioned()
     * @ORM\Column
     * @Assert\NotBlank
     */
    public $name = '';

    public function getId(): int
    {
        return $this->id;
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