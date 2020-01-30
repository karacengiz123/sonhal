<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * PsDomainAliases
 *@ApiResource()
 * @ORM\Table(name="ps_domain_aliases",options={"collate"="utf8_general_ci"})
 * @ORM\Entity
 */
class PsDomainAliases
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=40, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="domain", type="string", length=80, nullable=true)
     */
    private $domain;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(?string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }


}
