<?php
/**
 * Created by PhpStorm.
 * User: sarpdoruk
 * Date: 22.11.2018
 * Time: 08:11
 */

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\UserLogRepository")
 * @ORM\Table(name="user_log",options={"collate"="utf8_general_ci"})
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogUserLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt" ,timeAware=false)
 */
class UserLog
{
    use DateTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="changeLogUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $changeUser; // Değitirilen Kişi

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="changesLogUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $changedUser; // Değiştiren Kişi

    /**
     * @ORM\Column(type="text")
     */
    private $oldchangeUser; // Değitirilen Kişinin Eski Bilgisi

    /**
     * @ORM\Column(type="text")
     */
    private $newChangeUser; // Değitirilen Kişinin Yeni Bilgisi

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChangeUser(): ?User
    {
        return $this->changeUser;
    }

    public function setChangeUser(?User $changeUser): self
    {
        $this->changeUser = $changeUser;

        return $this;
    }

    public function getChangedUser(): ?User
    {
        return $this->changedUser;
    }

    public function setChangedUser(?User $changedUser): self
    {
        $this->changedUser = $changedUser;

        return $this;
    }

    public function getOldchangeUser(): ?string
    {
        return $this->oldchangeUser;
    }

    public function setOldchangeUser(string $oldchangeUser): self
    {
        $this->oldchangeUser = $oldchangeUser;

        return $this;
    }

    public function getNewChangeUser(): ?string
    {
        return $this->newChangeUser;
    }

    public function setNewChangeUser(string $newChangeUser): self
    {
        $this->newChangeUser = $newChangeUser;

        return $this;
    }
}