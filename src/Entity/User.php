<?php
// src/AppBundle/Entity/User.php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Asterisk\Entity\Extens;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user",options={"collate"="utf8_general_ci"})
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogUser")
 * @ORM\EntityListeners("App\EventListener\UserListener")
 */
class User extends BaseUser
{
    use DateTrait;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Group")
     * @ORM\JoinTable(name="user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AgentBreak", mappedBy="user")
     */
    private $agentBreaks;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserProfile", mappedBy="user", cascade={"persist", "remove"})
     * @Gedmo\Versioned()
     */
    private $userProfile;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Team", mappedBy="manager")
     */
    private $teams;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Team", mappedBy="user")
     */
    private $agentTeams;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BreakGroup", inversedBy="user")
     * @Gedmo\Versioned()
     */
    private $breakGroup;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team", inversedBy="users")
     * @Gedmo\Versioned()
     */
    private $teamId;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Team", mappedBy="managerBackup")
     */
    private $teamsBackup;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Evaluation", mappedBy="user")
     */
    private $evaluations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Evaluation", mappedBy="evaluative")
     */
    private $byevaluations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EvaluationAnswer", mappedBy="evaluative")
     */
    private $evaluationAnswers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LoginLog", mappedBy="userId")
     */
    private $loginLogs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RegisterLog", mappedBy="user")
     */
    private $registerLogs;


    /**
     * @ORM\Column(type="datetime",nullable=true)
     * @Gedmo\Versioned()
     */
    private $chatLastActivity;

    /**
     * @ORM\Column(type="smallint",nullable=true)
     * @Gedmo\Versioned()
     */
    private $chatStatus;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Chat", mappedBy="user")
     */
    private $chats;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AcwLog", mappedBy="user")
     */
    private $acwLogs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HoldLog", mappedBy="user")
     */
    private $holdLogs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Calls", mappedBy="user")
     */
    private $userCall;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RealtimeQueueMembers", mappedBy="user")
     */
    private $realtimeQueueMembers;

    /**
     * @ORM\OneToMany(targetEntity="App\Asterisk\Entity\Extens", mappedBy="user")
     */
    private $extens;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RecordListenLog", mappedBy="user")
     */
    private $recordListenLogs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Orders", mappedBy="teamLeader")
     */
    private $leaderOrdes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Orders", mappedBy="user")
     */
    private $orders;

    /**
     * @ORM\Column(type="smallint", nullable=true,options={"default"="0"})
     * @Gedmo\Versioned()
     */
    private $state;

    /**
     * @ORM\Column(type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     * @Gedmo\Versioned()
     */
    private $lastStateChange;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserLog", mappedBy="changeUser")
     */
    private $changeLogUsers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserLog", mappedBy="changedUser")
     */
    private $changesLogUsers;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserCategory", inversedBy="users")
     * @ORM\JoinColumn(nullable=true)
     * @Gedmo\Versioned()
     */
    private $userCategory;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StateLog", mappedBy="user")
     */
    private $stateLogs;

    public function __toString()
    {
        if($this->getUserProfile()) {
            return $this->getUserProfile()->getFirstName()." ".$this->getUserProfile()->getLastName();
        }

        return $this->getUsername()." Kullanıcı Profili Açılmamış";
    }


    public function __construct()
    {
        parent::__construct();
        $this->agentBreaks = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->agentTeams = new ArrayCollection();
        $this->teamsBackup = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
        $this->byevaluations = new ArrayCollection();
        $this->evaluationAnswers = new ArrayCollection();
        $this->loginLogs = new ArrayCollection();
        $this->registerLogs = new ArrayCollection();
        $this->chats = new ArrayCollection();
        $this->acwLogs = new ArrayCollection();
        $this->holdLogs = new ArrayCollection();
        $this->userCall = new ArrayCollection();
        $this->realtimeQueueMembers = new ArrayCollection();
        $this->extens = new ArrayCollection();
        $this->recordListenLogs = new ArrayCollection();
        $this->leaderOrdes = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->changeLogUsers = new ArrayCollection();
        $this->changesLogUsers = new ArrayCollection();
        $this->stateLogs = new ArrayCollection();
    }

    /**
     * @return Collection|AgentBreak[]
     */
    public function getAgentBreaks(): Collection
    {
        return $this->agentBreaks;
    }

    public function addAgentBreak(AgentBreak $agentBreak): self
    {
        if (!$this->agentBreaks->contains($agentBreak)) {
            $this->agentBreaks[] = $agentBreak;
            $agentBreak->setUser($this);
        }

        return $this;
    }

    public function removeAgentBreak(AgentBreak $agentBreak): self
    {
        if ($this->agentBreaks->contains($agentBreak)) {
            $this->agentBreaks->removeElement($agentBreak);
            // set the owning side to null (unless already changed)
            if ($agentBreak->getUser() === $this) {
                $agentBreak->setUser(null);
            }
        }

        return $this;
    }

    public function getUserProfile(): ?UserProfile
    {
        return $this->userProfile;
    }

    public function setUserProfile(?UserProfile $userProfile): self
    {
        $this->userProfile = $userProfile;

        // set (or unset) the owning side of the relation if necessary
        $newUser = $userProfile === null ? null : $this;
        if ($newUser !== $userProfile->getUser()) {
            $userProfile->setUser($newUser);
        }

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->setManager($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
            // set the owning side to null (unless already changed)
            if ($team->getManager() === $this) {
                $team->setManager(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getAgentTeams(): Collection
    {
        return $this->agentTeams;
    }

    public function addAgentTeam(Team $agentTeam): self
    {
        if (!$this->agentTeams->contains($agentTeam)) {
            $this->agentTeams[] = $agentTeam;
            $agentTeam->addUser($this);
        }

        return $this;
    }

    public function removeAgentTeam(Team $agentTeam): self
    {
        if ($this->agentTeams->contains($agentTeam)) {
            $this->agentTeams->removeElement($agentTeam);
            $agentTeam->removeUser($this);
        }

        return $this;
    }

    public function getBreakGroup(): ?BreakGroup
    {
        return $this->breakGroup;
    }

    public function setBreakGroup(?BreakGroup $breakGroup): self
    {
        $this->breakGroup = $breakGroup;

        return $this;
    }

    public function getTeamId(): ?Team
    {
        return $this->teamId;
    }

    public function setTeamId(?Team $teamId): self
    {
        $this->teamId = $teamId;

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getTeamsBackup(): Collection
    {
        return $this->teamsBackup;
    }

    public function addTeamsBackup(Team $teamsBackup): self
    {
        if (!$this->teamsBackup->contains($teamsBackup)) {
            $this->teamsBackup[] = $teamsBackup;
            $teamsBackup->setManagerBackup($this);
        }

        return $this;
    }

    public function removeTeamsBackup(Team $teamsBackup): self
    {
        if ($this->teamsBackup->contains($teamsBackup)) {
            $this->teamsBackup->removeElement($teamsBackup);
            // set the owning side to null (unless already changed)
            if ($teamsBackup->getManagerBackup() === $this) {
                $teamsBackup->setManagerBackup(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(GroupInterface $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
        }

        return $this;
    }

    public function removeGroup(GroupInterface $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
        }

        return $this;
    }

    /**
     * @return Collection|Evaluation[]
     */
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(Evaluation $evaluation): self
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations[] = $evaluation;
            $evaluation->setUser($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): self
    {
        if ($this->evaluations->contains($evaluation)) {
            $this->evaluations->removeElement($evaluation);
            // set the owning side to null (unless already changed)
            if ($evaluation->getUser() === $this) {
                $evaluation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Evaluation[]
     */
    public function getByevaluations(): Collection
    {
        return $this->byevaluations;
    }

    public function addByevaluation(Evaluation $byevaluation): self
    {
        if (!$this->byevaluations->contains($byevaluation)) {
            $this->byevaluations[] = $byevaluation;
            $byevaluation->setEvaluative($this);
        }

        return $this;
    }

    public function removeByevaluation(Evaluation $byevaluation): self
    {
        if ($this->byevaluations->contains($byevaluation)) {
            $this->byevaluations->removeElement($byevaluation);
            // set the owning side to null (unless already changed)
            if ($byevaluation->getEvaluative() === $this) {
                $byevaluation->setEvaluative(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EvaluationAnswer[]
     */
    public function getEvaluationAnswers(): Collection
    {
        return $this->evaluationAnswers;
    }

    public function addEvaluationAnswer(EvaluationAnswer $evaluationAnswer): self
    {
        if (!$this->evaluationAnswers->contains($evaluationAnswer)) {
            $this->evaluationAnswers[] = $evaluationAnswer;
            $evaluationAnswer->setEvaluative($this);
        }

        return $this;
    }

    public function removeEvaluationAnswer(EvaluationAnswer $evaluationAnswer): self
    {
        if ($this->evaluationAnswers->contains($evaluationAnswer)) {
            $this->evaluationAnswers->removeElement($evaluationAnswer);
            // set the owning side to null (unless already changed)
            if ($evaluationAnswer->getEvaluative() === $this) {
                $evaluationAnswer->setEvaluative(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LoginLog[]
     */
    public function getLoginLogs(): Collection
    {
        return $this->loginLogs;
    }

    public function addLoginLog(LoginLog $loginLog): self
    {
        if (!$this->loginLogs->contains($loginLog)) {
            $this->loginLogs[] = $loginLog;
            $loginLog->setUserId($this);
        }

        return $this;
    }

    public function removeLoginLog(LoginLog $loginLog): self
    {
        if ($this->loginLogs->contains($loginLog)) {
            $this->loginLogs->removeElement($loginLog);
            // set the owning side to null (unless already changed)
            if ($loginLog->getUserId() === $this) {
                $loginLog->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RegisterLog[]
     */
    public function getRegisterLogs(): Collection
    {
        return $this->registerLogs;
    }

    public function addRegisterLog(RegisterLog $registerLog): self
    {
        if (!$this->registerLogs->contains($registerLog)) {
            $this->registerLogs[] = $registerLog;
            $registerLog->setUserId($this);
        }

        return $this;
    }

    public function removeRegisterLog(RegisterLog $registerLog): self
    {
        if ($this->registerLogs->contains($registerLog)) {
            $this->registerLogs->removeElement($registerLog);
            // set the owning side to null (unless already changed)
            if ($registerLog->getUserId() === $this) {
                $registerLog->setUserId(null);
            }
        }

        return $this;
    }

    public function getChatLastActivity(): ?\DateTimeInterface
    {
        return $this->chatLastActivity;
    }

    public function setChatLastActivity(\DateTimeInterface $chatLastActivity): self
    {
        $this->chatLastActivity = $chatLastActivity;

        return $this;
    }

    public function getChatStatus(): ?int
    {
        return $this->chatStatus;
    }

    public function setChatStatus(int $chatStatus): self
    {
        $this->chatStatus = $chatStatus;

        return $this;
    }

    /**
     * @return Collection|Chat[]
     */
    public function getChats(): Collection
    {
        return $this->chats;
    }

    public function addChat(Chat $chat): self
    {
        if (!$this->chats->contains($chat)) {
            $this->chats[] = $chat;
            $chat->setUser($this);
        }

        return $this;
    }

    public function removeChat(Chat $chat): self
    {
        if ($this->chats->contains($chat)) {
            $this->chats->removeElement($chat);
            // set the owning side to null (unless already changed)
            if ($chat->getUser() === $this) {
                $chat->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AcwLog[]
     */
    public function getAcwLogs(): Collection
    {
        return $this->acwLogs;
    }

    public function addAcwLog(AcwLog $acwLog): self
    {
        if (!$this->acwLogs->contains($acwLog)) {
            $this->acwLogs[] = $acwLog;
            $acwLog->setUser($this);
        }

        return $this;
    }

    public function removeAcwLog(AcwLog $acwLog): self
    {
        if ($this->acwLogs->contains($acwLog)) {
            $this->acwLogs->removeElement($acwLog);
            // set the owning side to null (unless already changed)
            if ($acwLog->getUser() === $this) {
                $acwLog->setUser(null);
            }
        }

        return $this;
    }





    /**
     * @return Collection|HoldLog[]
     */
    public function getHoldLogs(): Collection
    {
        return $this->holdLogs;
    }

    public function addHoldLog(HoldLog $holdLog): self
    {
        if (!$this->holdLogs->contains($holdLog)) {
            $this->holdLogs[] = $holdLog;
            $holdLog->setUser($this);
        }

        return $this;
    }

    public function removeHoldLog(HoldLog $holdLog): self
    {
        if ($this->holdLogs->contains($holdLog)) {
            $this->holdLogs->removeElement($holdLog);
            // set the owning side to null (unless already changed)
            if ($holdLog->getUser() === $this) {
                $holdLog->setUser(null);
            }
        }

        return $this;
    }










    /**
     * @return Collection|Calls[]
     */
    public function getUserCall(): Collection
    {
        return $this->userCall;
    }

    public function addUserCall(Calls $userCall): self
    {
        if (!$this->userCall->contains($userCall)) {
            $this->userCall[] = $userCall;
            $userCall->setUser($this);
        }

        return $this;
    }

    public function removeUserCall(Calls $userCall): self
    {
        if ($this->userCall->contains($userCall)) {
            $this->userCall->removeElement($userCall);
            // set the owning side to null (unless already changed)
            if ($userCall->getUser() === $this) {
                $userCall->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RealtimeQueueMembers[]
     */
    public function getRealtimeQueueMembers(): Collection
    {
        return $this->realtimeQueueMembers;
    }

    public function addRealtimeQueueMember(RealtimeQueueMembers $realtimeQueueMember): self
    {
        if (!$this->realtimeQueueMembers->contains($realtimeQueueMember)) {
            $this->realtimeQueueMembers[] = $realtimeQueueMember;
            $realtimeQueueMember->setUser($this);
        }

        return $this;
    }

    public function removeRealtimeQueueMember(RealtimeQueueMembers $realtimeQueueMember): self
    {
        if ($this->realtimeQueueMembers->contains($realtimeQueueMember)) {
            $this->realtimeQueueMembers->removeElement($realtimeQueueMember);
            // set the owning side to null (unless already changed)
            if ($realtimeQueueMember->getUser() === $this) {
                $realtimeQueueMember->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Extens[]
     */
    public function getExtens(): Collection
    {
        return $this->extens;
    }

    public function addExten(Extens $exten): self
    {
        if (!$this->extens->contains($exten)) {
            $this->extens[] = $exten;
            $exten->setUser($this);
        }

        return $this;
    }

    public function removeExten(Extens $exten): self
    {
        if ($this->extens->contains($exten)) {
            $this->extens->removeElement($exten);
            // set the owning side to null (unless already changed)
            if ($exten->getUser() === $this) {
                $exten->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RecordListenLog[]
     */
    public function getRecordListenLogs(): Collection
    {
        return $this->recordListenLogs;
    }

    public function addRecordListenLog(RecordListenLog $recordListenLog): self
    {
        if (!$this->recordListenLogs->contains($recordListenLog)) {
            $this->recordListenLogs[] = $recordListenLog;
            $recordListenLog->setUser($this);
        }

        return $this;
    }

    public function removeRecordListenLog(RecordListenLog $recordListenLog): self
    {
        if ($this->recordListenLogs->contains($recordListenLog)) {
            $this->recordListenLogs->removeElement($recordListenLog);
            // set the owning side to null (unless already changed)
            if ($recordListenLog->getUser() === $this) {
                $recordListenLog->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Orders[]
     */
    public function getLeaderOrdes(): Collection
    {
        return $this->leaderOrdes;
    }

    public function addLeaderOrde(Orders $leaderOrde): self
    {
        if (!$this->leaderOrdes->contains($leaderOrde)) {
            $this->leaderOrdes[] = $leaderOrde;
            $leaderOrde->setTeamLeader($this);
        }

        return $this;
    }

    public function removeLeaderOrde(Orders $leaderOrde): self
    {
        if ($this->leaderOrdes->contains($leaderOrde)) {
            $this->leaderOrdes->removeElement($leaderOrde);
            // set the owning side to null (unless already changed)
            if ($leaderOrde->getTeamLeader() === $this) {
                $leaderOrde->setTeamLeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Orders[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Orders $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Orders $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(?int $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getLastStateChange(): ?\DateTimeInterface
    {
        return $this->lastStateChange;
    }

    public function setLastStateChange(?\DateTimeInterface $lastStateChange): self
    {
        $this->lastStateChange = $lastStateChange;

        return $this;
    }

    /**
     * @return Collection|UserLog[]
     */
    public function getChangeLogUsers(): Collection
    {
        return $this->changeLogUsers;
    }

    public function addChangeLogUser(UserLog $changeLogUser): self
    {
        if (!$this->changeLogUsers->contains($changeLogUser)) {
            $this->changeLogUsers[] = $changeLogUser;
            $changeLogUser->setChangeUser($this);
        }

        return $this;
    }

    public function removeChangeLogUser(UserLog $changeLogUser): self
    {
        if ($this->changeLogUsers->contains($changeLogUser)) {
            $this->changeLogUsers->removeElement($changeLogUser);
            // set the owning side to null (unless already changed)
            if ($changeLogUser->getChangeUser() === $this) {
                $changeLogUser->setChangeUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserLog[]
     */
    public function getChangesLogUsers(): Collection
    {
        return $this->changesLogUsers;
    }

    public function addChangesLogUser(UserLog $changesLogUser): self
    {
        if (!$this->changesLogUsers->contains($changesLogUser)) {
            $this->changesLogUsers[] = $changesLogUser;
            $changesLogUser->setChangedUser($this);
        }

        return $this;
    }

    public function removeChangesLogUser(UserLog $changesLogUser): self
    {
        if ($this->changesLogUsers->contains($changesLogUser)) {
            $this->changesLogUsers->removeElement($changesLogUser);
            // set the owning side to null (unless already changed)
            if ($changesLogUser->getChangedUser() === $this) {
                $changesLogUser->setChangedUser(null);
            }
        }

        return $this;
    }

    public function getUserCategory(): ?UserCategory
    {
        return $this->userCategory;
    }

    public function setUserCategory(?UserCategory $userCategory): self
    {
        $this->userCategory = $userCategory;

        return $this;
    }

    /**
     * @return Collection|StateLog[]
     */
    public function getStateLogs(): Collection
    {
        return $this->stateLogs;
    }

    public function addStateLog(StateLog $stateLog): self
    {
        if (!$this->stateLogs->contains($stateLog)) {
            $this->stateLogs[] = $stateLog;
            $stateLog->setUser($this);
        }

        return $this;
    }

    public function removeStateLog(StateLog $stateLog): self
    {
        if ($this->stateLogs->contains($stateLog)) {
            $this->stateLogs->removeElement($stateLog);
            // set the owning side to null (unless already changed)
            if ($stateLog->getUser() === $this) {
                $stateLog->setUser(null);
            }
        }

        return $this;
    }
}