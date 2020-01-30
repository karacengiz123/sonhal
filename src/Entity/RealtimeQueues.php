<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ApiResource()
 * RealtimeQueues
 * @ORM\Entity(repositoryClass="App\Repository\RealtimeQueuesRepository")
 * @ORM\Table(name="realtime_queues",options={"collate"="utf8_general_ci"})
 * @ORM\Entity
 * @Gedmo\Loggable(logEntryClass="App\Entity\LogRealtimeQueues")
 */
class RealtimeQueues
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Gedmo\Versioned()
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="musiconhold", type="string", length=128, nullable=true, options={"default"="default"})
     * @Gedmo\Versioned()
     */
    private $musiconhold = 'default';

    /**
     * @var string|null
     *
     * @ORM\Column(name="announce", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $announce;

    /**
     * @var string|null
     *
     * @ORM\Column(name="context", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $context;

    /**
     * @var int|null
     *
     * @ORM\Column(name="timeout", type="integer", nullable=true)
     * @Gedmo\Versioned()
     */
    private $timeout;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ringinuse", type="string", length=0, nullable=true)
     * @Gedmo\Versioned()
     */
    private $ringinuse;

    /**
     * @var string|null
     *
     * @ORM\Column(name="setinterfacevar", type="string", length=0, nullable=true)
     * @Gedmo\Versioned()
     */
    private $setinterfacevar;

    /**
     * @var string|null
     *
     * @ORM\Column(name="setqueuevar", type="string", length=0, nullable=true)
     * @Gedmo\Versioned()
     */
    private $setqueuevar;

    /**
     * @var string|null
     *
     * @ORM\Column(name="setqueueentryvar", type="string", length=0, nullable=true)
     * @Gedmo\Versioned()
     */
    private $setqueueentryvar;

    /**
     * @var string|null
     *
     * @ORM\Column(name="monitor_format", type="string", length=8, nullable=true)
     * @Gedmo\Versioned()
     */
    private $monitorFormat;

    /**
     * @var string|null
     *
     * @ORM\Column(name="membermacro", type="string", length=512, nullable=true)
     * @Gedmo\Versioned()
     */
    private $membermacro;

    /**
     * @var string|null
     *
     * @ORM\Column(name="membergosub", type="string", length=512, nullable=true)
     * @Gedmo\Versioned()
     */
    private $membergosub;

    /**
     * @var string|null
     *
     * @ORM\Column(name="queue_youarenext", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $queueYouarenext;

    /**
     * @var string|null
     *
     * @ORM\Column(name="queue_thereare", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $queueThereare;

    /**
     * @var string|null
     *
     * @ORM\Column(name="queue_callswaiting", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $queueCallswaiting;

    /**
     * @var string|null
     *
     * @ORM\Column(name="queue_quantity1", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $queueQuantity1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="queue_quantity2", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $queueQuantity2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="queue_holdtime", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $queueHoldtime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="queue_minutes", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $queueMinutes;

    /**
     * @var string|null
     *
     * @ORM\Column(name="queue_minute", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $queueMinute;

    /**
     * @var string|null
     *
     * @ORM\Column(name="queue_seconds", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $queueSeconds;

    /**
     * @var string|null
     *
     * @ORM\Column(name="queue_thankyou", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $queueThankyou;

    /**
     * @var string|null
     *
     * @ORM\Column(name="queue_callerannounce", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $queueCallerannounce;

    /**
     * @var string|null
     *
     * @ORM\Column(name="queue_reporthold", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $queueReporthold;

    /**
     * @var int|null
     *
     * @ORM\Column(name="announce_frequency", type="integer", nullable=true)
     * @Gedmo\Versioned()
     */
    private $announceFrequency;

    /**
     * @var string|null
     *
     * @ORM\Column(name="announce_to_first_user", type="string", length=0, nullable=true)
     * @Gedmo\Versioned()
     */
    private $announceToFirstUser;

    /**
     * @var int|null
     *
     * @ORM\Column(name="min_announce_frequency", type="integer", nullable=true)
     * @Gedmo\Versioned()
     */
    private $minAnnounceFrequency;

    /**
     * @var int|null
     *
     * @ORM\Column(name="announce_round_seconds", type="integer", nullable=true)
     * @Gedmo\Versioned()
     */
    private $announceRoundSeconds;

    /**
     * @var string|null
     *
     * @ORM\Column(name="announce_holdtime", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $announceHoldtime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="announce_position", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $announcePosition;

    /**
     * @var int|null
     *
     * @ORM\Column(name="announce_position_limit", type="integer", nullable=true)
     * @Gedmo\Versioned()
     */
    private $announcePositionLimit;

    /**
     * @var string|null
     *
     * @ORM\Column(name="periodic_announce", type="string", length=50, nullable=true)
     * @Gedmo\Versioned()
     */
    private $periodicAnnounce;

    /**
     * @var int|null
     *
     * @ORM\Column(name="periodic_announce_frequency", type="integer", nullable=true)
     * @Gedmo\Versioned()
     */
    private $periodicAnnounceFrequency;

    /**
     * @var string|null
     *
     * @ORM\Column(name="relative_periodic_announce", type="string", length=0, nullable=true)
     * @Gedmo\Versioned()
     */
    private $relativePeriodicAnnounce;

    /**
     * @var string|null
     *
     * @ORM\Column(name="random_periodic_announce", type="string", length=0, nullable=true)
     * @Gedmo\Versioned()
     */
    private $randomPeriodicAnnounce;

    /**
     * @var int|null
     *
     * @ORM\Column(name="retry", type="integer", nullable=true)
     * @Gedmo\Versioned()
     */
    private $retry;

    /**
     * @var int|null
     *
     * @ORM\Column(name="wrapuptime", type="integer", nullable=true)
     * @Gedmo\Versioned()
     */
    private $wrapuptime;

    /**
     * @var int|null
     *
     * @ORM\Column(name="penaltymemberslimit", type="integer", nullable=true)
     * @Gedmo\Versioned()
     */
    private $penaltymemberslimit;

    /**
     * @var string|null
     *
     * @ORM\Column(name="autofill", type="string", length=0, nullable=true)
     * @Gedmo\Versioned()
     */
    private $autofill;

    /**
     * @var string|null
     *
     * @ORM\Column(name="monitor_type", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $monitorType;

    /**
     * @var string|null
     *
     * @ORM\Column(name="autopause", type="string", length=0, nullable=true)
     * @Gedmo\Versioned()
     */
    private $autopause;

    /**
     * @var int|null
     *
     * @ORM\Column(name="autopausedelay", type="integer", nullable=true)
     * @Gedmo\Versioned()
     */
    private $autopausedelay;

    /**
     * @var string|null
     *
     * @ORM\Column(name="autopausebusy", type="string", length=0, nullable=true)
     * @Gedmo\Versioned()
     */
    private $autopausebusy;

    /**
     * @var string|null
     *
     * @ORM\Column(name="autopauseunavail", type="string", length=0, nullable=true)
     * @Gedmo\Versioned()
     */
    private $autopauseunavail;

    /**
     * @var int|null
     *
     * @ORM\Column(name="maxlen", type="integer", nullable=true)
     * @Gedmo\Versioned()
     */
    private $maxlen;

    /**
     * @var int|null
     *
     * @ORM\Column(name="servicelevel", type="integer", nullable=true)
     * @Gedmo\Versioned()
     */
    private $servicelevel;

    /**
     * @var string|null
     *
     * @ORM\Column(name="strategy", type="string", length=0, nullable=true, options={"default"="rrmemory"})
     * @Gedmo\Versioned()
     */
    private $strategy = 'rrmemory';

    /**
     * @var string|null
     *
     * @ORM\Column(name="joinempty", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $joinempty;

    /**
     * @var string|null
     *
     * @ORM\Column(name="leavewhenempty", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $leavewhenempty;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reportholdtime", type="string", length=0, nullable=true)
     * @Gedmo\Versioned()
     */
    private $reportholdtime;

    /**
     * @var int|null
     *
     * @ORM\Column(name="memberdelay", type="integer", nullable=true)
     * @Gedmo\Versioned()
     */
    private $memberdelay;

    /**
     * @var int|null
     *
     * @ORM\Column(name="weight", type="integer", nullable=true)
     * @Gedmo\Versioned()
     */
    private $weight;

    /**
     * @var string|null
     *
     * @ORM\Column(name="timeoutrestart", type="string", length=0, nullable=true)
     * @Gedmo\Versioned()
     */
    private $timeoutrestart;

    /**
     * @var string|null
     *
     * @ORM\Column(name="defaultrule", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $defaultrule;

    /**
     * @var string|null
     *
     * @ORM\Column(name="timeoutpriority", type="string", length=128, nullable=true)
     * @Gedmo\Versioned()
     */
    private $timeoutpriority;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getMusiconhold(): ?string
    {
        return $this->musiconhold;
    }

    public function setMusiconhold(?string $musiconhold): self
    {
        $this->musiconhold = $musiconhold;

        return $this;
    }

    public function getAnnounce(): ?string
    {
        return $this->announce;
    }

    public function setAnnounce(?string $announce): self
    {
        $this->announce = $announce;

        return $this;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(?string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getTimeout(): ?int
    {
        return $this->timeout;
    }

    public function setTimeout(?int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function getRinginuse(): ?string
    {
        return $this->ringinuse;
    }

    public function setRinginuse(?string $ringinuse): self
    {
        $this->ringinuse = $ringinuse;

        return $this;
    }

    public function getSetinterfacevar(): ?string
    {
        return $this->setinterfacevar;
    }

    public function setSetinterfacevar(?string $setinterfacevar): self
    {
        $this->setinterfacevar = $setinterfacevar;

        return $this;
    }

    public function getSetqueuevar(): ?string
    {
        return $this->setqueuevar;
    }

    public function setSetqueuevar(?string $setqueuevar): self
    {
        $this->setqueuevar = $setqueuevar;

        return $this;
    }

    public function getSetqueueentryvar(): ?string
    {
        return $this->setqueueentryvar;
    }

    public function setSetqueueentryvar(?string $setqueueentryvar): self
    {
        $this->setqueueentryvar = $setqueueentryvar;

        return $this;
    }

    public function getMonitorFormat(): ?string
    {
        return $this->monitorFormat;
    }

    public function setMonitorFormat(?string $monitorFormat): self
    {
        $this->monitorFormat = $monitorFormat;

        return $this;
    }

    public function getMembermacro(): ?string
    {
        return $this->membermacro;
    }

    public function setMembermacro(?string $membermacro): self
    {
        $this->membermacro = $membermacro;

        return $this;
    }

    public function getMembergosub(): ?string
    {
        return $this->membergosub;
    }

    public function setMembergosub(?string $membergosub): self
    {
        $this->membergosub = $membergosub;

        return $this;
    }

    public function getQueueYouarenext(): ?string
    {
        return $this->queueYouarenext;
    }

    public function setQueueYouarenext(?string $queueYouarenext): self
    {
        $this->queueYouarenext = $queueYouarenext;

        return $this;
    }

    public function getQueueThereare(): ?string
    {
        return $this->queueThereare;
    }

    public function setQueueThereare(?string $queueThereare): self
    {
        $this->queueThereare = $queueThereare;

        return $this;
    }

    public function getQueueCallswaiting(): ?string
    {
        return $this->queueCallswaiting;
    }

    public function setQueueCallswaiting(?string $queueCallswaiting): self
    {
        $this->queueCallswaiting = $queueCallswaiting;

        return $this;
    }

    public function getQueueQuantity1(): ?string
    {
        return $this->queueQuantity1;
    }

    public function setQueueQuantity1(?string $queueQuantity1): self
    {
        $this->queueQuantity1 = $queueQuantity1;

        return $this;
    }

    public function getQueueQuantity2(): ?string
    {
        return $this->queueQuantity2;
    }

    public function setQueueQuantity2(?string $queueQuantity2): self
    {
        $this->queueQuantity2 = $queueQuantity2;

        return $this;
    }

    public function getQueueHoldtime(): ?string
    {
        return $this->queueHoldtime;
    }

    public function setQueueHoldtime(?string $queueHoldtime): self
    {
        $this->queueHoldtime = $queueHoldtime;

        return $this;
    }

    public function getQueueMinutes(): ?string
    {
        return $this->queueMinutes;
    }

    public function setQueueMinutes(?string $queueMinutes): self
    {
        $this->queueMinutes = $queueMinutes;

        return $this;
    }

    public function getQueueMinute(): ?string
    {
        return $this->queueMinute;
    }

    public function setQueueMinute(?string $queueMinute): self
    {
        $this->queueMinute = $queueMinute;

        return $this;
    }

    public function getQueueSeconds(): ?string
    {
        return $this->queueSeconds;
    }

    public function setQueueSeconds(?string $queueSeconds): self
    {
        $this->queueSeconds = $queueSeconds;

        return $this;
    }

    public function getQueueThankyou(): ?string
    {
        return $this->queueThankyou;
    }

    public function setQueueThankyou(?string $queueThankyou): self
    {
        $this->queueThankyou = $queueThankyou;

        return $this;
    }

    public function getQueueCallerannounce(): ?string
    {
        return $this->queueCallerannounce;
    }

    public function setQueueCallerannounce(?string $queueCallerannounce): self
    {
        $this->queueCallerannounce = $queueCallerannounce;

        return $this;
    }

    public function getQueueReporthold(): ?string
    {
        return $this->queueReporthold;
    }

    public function setQueueReporthold(?string $queueReporthold): self
    {
        $this->queueReporthold = $queueReporthold;

        return $this;
    }

    public function getAnnounceFrequency(): ?int
    {
        return $this->announceFrequency;
    }

    public function setAnnounceFrequency(?int $announceFrequency): self
    {
        $this->announceFrequency = $announceFrequency;

        return $this;
    }

    public function getAnnounceToFirstUser(): ?string
    {
        return $this->announceToFirstUser;
    }

    public function setAnnounceToFirstUser(?string $announceToFirstUser): self
    {
        $this->announceToFirstUser = $announceToFirstUser;

        return $this;
    }

    public function getMinAnnounceFrequency(): ?int
    {
        return $this->minAnnounceFrequency;
    }

    public function setMinAnnounceFrequency(?int $minAnnounceFrequency): self
    {
        $this->minAnnounceFrequency = $minAnnounceFrequency;

        return $this;
    }

    public function getAnnounceRoundSeconds(): ?int
    {
        return $this->announceRoundSeconds;
    }

    public function setAnnounceRoundSeconds(?int $announceRoundSeconds): self
    {
        $this->announceRoundSeconds = $announceRoundSeconds;

        return $this;
    }

    public function getAnnounceHoldtime(): ?string
    {
        return $this->announceHoldtime;
    }

    public function setAnnounceHoldtime(?string $announceHoldtime): self
    {
        $this->announceHoldtime = $announceHoldtime;

        return $this;
    }

    public function getAnnouncePosition(): ?string
    {
        return $this->announcePosition;
    }

    public function setAnnouncePosition(?string $announcePosition): self
    {
        $this->announcePosition = $announcePosition;

        return $this;
    }

    public function getAnnouncePositionLimit(): ?int
    {
        return $this->announcePositionLimit;
    }

    public function setAnnouncePositionLimit(?int $announcePositionLimit): self
    {
        $this->announcePositionLimit = $announcePositionLimit;

        return $this;
    }

    public function getPeriodicAnnounce(): ?string
    {
        return $this->periodicAnnounce;
    }

    public function setPeriodicAnnounce(?string $periodicAnnounce): self
    {
        $this->periodicAnnounce = $periodicAnnounce;

        return $this;
    }

    public function getPeriodicAnnounceFrequency(): ?int
    {
        return $this->periodicAnnounceFrequency;
    }

    public function setPeriodicAnnounceFrequency(?int $periodicAnnounceFrequency): self
    {
        $this->periodicAnnounceFrequency = $periodicAnnounceFrequency;

        return $this;
    }

    public function getRelativePeriodicAnnounce(): ?string
    {
        return $this->relativePeriodicAnnounce;
    }

    public function setRelativePeriodicAnnounce(?string $relativePeriodicAnnounce): self
    {
        $this->relativePeriodicAnnounce = $relativePeriodicAnnounce;

        return $this;
    }

    public function getRandomPeriodicAnnounce(): ?string
    {
        return $this->randomPeriodicAnnounce;
    }

    public function setRandomPeriodicAnnounce(?string $randomPeriodicAnnounce): self
    {
        $this->randomPeriodicAnnounce = $randomPeriodicAnnounce;

        return $this;
    }

    public function getRetry(): ?int
    {
        return $this->retry;
    }

    public function setRetry(?int $retry): self
    {
        $this->retry = $retry;

        return $this;
    }

    public function getWrapuptime(): ?int
    {
        return $this->wrapuptime;
    }

    public function setWrapuptime(?int $wrapuptime): self
    {
        $this->wrapuptime = $wrapuptime;

        return $this;
    }

    public function getPenaltymemberslimit(): ?int
    {
        return $this->penaltymemberslimit;
    }

    public function setPenaltymemberslimit(?int $penaltymemberslimit): self
    {
        $this->penaltymemberslimit = $penaltymemberslimit;

        return $this;
    }

    public function getAutofill(): ?string
    {
        return $this->autofill;
    }

    public function setAutofill(?string $autofill): self
    {
        $this->autofill = $autofill;

        return $this;
    }

    public function getMonitorType(): ?string
    {
        return $this->monitorType;
    }

    public function setMonitorType(?string $monitorType): self
    {
        $this->monitorType = $monitorType;

        return $this;
    }

    public function getAutopause(): ?string
    {
        return $this->autopause;
    }

    public function setAutopause(?string $autopause): self
    {
        $this->autopause = $autopause;

        return $this;
    }

    public function getAutopausedelay(): ?int
    {
        return $this->autopausedelay;
    }

    public function setAutopausedelay(?int $autopausedelay): self
    {
        $this->autopausedelay = $autopausedelay;

        return $this;
    }

    public function getAutopausebusy(): ?string
    {
        return $this->autopausebusy;
    }

    public function setAutopausebusy(?string $autopausebusy): self
    {
        $this->autopausebusy = $autopausebusy;

        return $this;
    }

    public function getAutopauseunavail(): ?string
    {
        return $this->autopauseunavail;
    }

    public function setAutopauseunavail(?string $autopauseunavail): self
    {
        $this->autopauseunavail = $autopauseunavail;

        return $this;
    }

    public function getMaxlen(): ?int
    {
        return $this->maxlen;
    }

    public function setMaxlen(?int $maxlen): self
    {
        $this->maxlen = $maxlen;

        return $this;
    }

    public function getServicelevel(): ?int
    {
        return $this->servicelevel;
    }

    public function setServicelevel(?int $servicelevel): self
    {
        $this->servicelevel = $servicelevel;

        return $this;
    }

    public function getStrategy(): ?string
    {
        return $this->strategy;
    }

    public function setStrategy(?string $strategy): self
    {
        $this->strategy = $strategy;

        return $this;
    }

    public function getJoinempty(): ?string
    {
        return $this->joinempty;
    }

    public function setJoinempty(?string $joinempty): self
    {
        $this->joinempty = $joinempty;

        return $this;
    }

    public function getLeavewhenempty(): ?string
    {
        return $this->leavewhenempty;
    }

    public function setLeavewhenempty(?string $leavewhenempty): self
    {
        $this->leavewhenempty = $leavewhenempty;

        return $this;
    }

    public function getReportholdtime(): ?string
    {
        return $this->reportholdtime;
    }

    public function setReportholdtime(?string $reportholdtime): self
    {
        $this->reportholdtime = $reportholdtime;

        return $this;
    }

    public function getMemberdelay(): ?int
    {
        return $this->memberdelay;
    }

    public function setMemberdelay(?int $memberdelay): self
    {
        $this->memberdelay = $memberdelay;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getTimeoutrestart(): ?string
    {
        return $this->timeoutrestart;
    }

    public function setTimeoutrestart(?string $timeoutrestart): self
    {
        $this->timeoutrestart = $timeoutrestart;

        return $this;
    }

    public function getDefaultrule(): ?string
    {
        return $this->defaultrule;
    }

    public function setDefaultrule(?string $defaultrule): self
    {
        $this->defaultrule = $defaultrule;

        return $this;
    }

    public function getTimeoutpriority(): ?string
    {
        return $this->timeoutpriority;
    }

    public function setTimeoutpriority(?string $timeoutpriority): self
    {
        $this->timeoutpriority = $timeoutpriority;

        return $this;
    }


}
