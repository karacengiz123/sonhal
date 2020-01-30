<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Cdr
 * @ApiResource()
 * @ORM\Table(options={"collate"="utf8_general_ci"}, name="cdr", indexes={@ORM\Index(name="i_accountcode", columns={"accountcode"}), @ORM\Index(name="i_amaflags", columns={"amaflags"}), @ORM\Index(name="i_calldate", columns={"calldate"}), @ORM\Index(name="i_did", columns={"did"}), @ORM\Index(name="i_disposition", columns={"disposition"}), @ORM\Index(name="i_dst", columns={"dst"}), @ORM\Index(name="i_scode", columns={"scode"}), @ORM\Index(name="i_src", columns={"src"}), @ORM\Index(name="i_userfield", columns={"userfield"})})
 * @ORM\Entity(repositoryClass="App\Asterisk\Repository\CdrRepository")
 * @Gedmo\Loggable()
 */
class Cdr
{
    /**
     * @var int
     *
     * @ORM\Column(name="cdr_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $cdrId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="calldate", type="datetime", nullable=false)
     * @Gedmo\Versioned()
     */
    private $calldate;

    /**
     * @var string
     *
     * @ORM\Column(name="clid", type="string", length=80, nullable=false)
     * @Gedmo\Versioned()
     */
    private $clid = '';

    /**
     * @var string
     *
     * @ORM\Column(name="src", type="string", length=80, nullable=false)
     * @Gedmo\Versioned()
     */
    private $src = '';

    /**
     * @var string
     *
     * @ORM\Column(name="dst", type="string", length=80, nullable=false)
     * @Gedmo\Versioned()
     */
    private $dst = '';

    /**
     * @var string
     *
     * @ORM\Column(name="dcontext", type="string", length=80, nullable=false)
     * @Gedmo\Versioned()
     */
    private $dcontext = '';

    /**
     * @var string
     *
     * @ORM\Column(name="channel", type="string", length=80, nullable=false)
     * @Gedmo\Versioned()
     */
    private $channel = '';

    /**
     * @var string
     *
     * @ORM\Column(name="dstchannel", type="string", length=80, nullable=false)
     * @Gedmo\Versioned()
     */
    private $dstchannel = '';

    /**
     * @var string
     *
     * @ORM\Column(name="lastapp", type="string", length=80, nullable=false)
     * @Gedmo\Versioned()
     */
    private $lastapp = '';

    /**
     * @var string
     *
     * @ORM\Column(name="lastdata", type="string", length=80, nullable=false)
     * @Gedmo\Versioned()
     */
    private $lastdata = '';

    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="integer", nullable=false)
     * @Gedmo\Versioned()
     */
    private $duration = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="billsec", type="integer", nullable=false)
     * @Gedmo\Versioned()
     */
    private $billsec = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="disposition", type="string", length=45, nullable=false)
     * @Gedmo\Versioned()
     */
    private $disposition = '';

    /**
     * @var int
     *
     * @ORM\Column(name="amaflags", type="integer", nullable=false)
     * @Gedmo\Versioned()
     */
    private $amaflags = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="accountcode", type="string", length=20, nullable=false)
     * @Gedmo\Versioned()
     */
    private $accountcode = '';

    /**
     * @var string
     *
     * @ORM\Column(name="uniqueid", type="string", length=32, nullable=false)
     * @Gedmo\Versioned()
     */
    private $uniqueid = '';

    /**
     * @var string
     *
     * @ORM\Column(name="userfield", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $userfield = '';

    /**
     * @var string
     *
     * @ORM\Column(name="did", type="string", length=12, nullable=true, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $did = '';

    /**
     * @var string
     *
     * @ORM\Column(name="scode", type="string", length=10, nullable=true, options={"fixed"=true})
     * @Gedmo\Versioned()
     */
    private $scode = '';

    /**
     * @var string
     *
     * @ORM\Column(name="src_org", type="string", length=80, nullable=true)
     * @Gedmo\Versioned()
     */
    private $srcOrg = '';

    /**
     * @var string
     *
     * @ORM\Column(name="dst_org", type="string", length=80, nullable=true)
     * @Gedmo\Versioned()
     */
    private $dstOrg = '';

    /**
     * @var string
     *
     * @ORM\Column(name="call_id", type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $callId = '';

    public function getCdrId(): ?int
    {
        return $this->cdrId;
    }

    public function getCalldate(): ?\DateTimeInterface
    {
        return $this->calldate;
    }

    public function setCalldate(\DateTimeInterface $calldate): self
    {
        $this->calldate = $calldate;

        return $this;
    }

    public function getClid(): ?string
    {
        return $this->clid;
    }

    public function setClid(string $clid): self
    {
        $this->clid = $clid;

        return $this;
    }

    public function getSrc(): ?string
    {
        return $this->src;
    }

    public function setSrc(string $src): self
    {
        $this->src = $src;

        return $this;
    }

    public function getDst(): ?string
    {
        return $this->dst;
    }

    public function setDst(string $dst): self
    {
        $this->dst = $dst;

        return $this;
    }

    public function getDcontext(): ?string
    {
        return $this->dcontext;
    }

    public function setDcontext(string $dcontext): self
    {
        $this->dcontext = $dcontext;

        return $this;
    }

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function setChannel(string $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function getDstchannel(): ?string
    {
        return $this->dstchannel;
    }

    public function setDstchannel(string $dstchannel): self
    {
        $this->dstchannel = $dstchannel;

        return $this;
    }

    public function getLastapp(): ?string
    {
        return $this->lastapp;
    }

    public function setLastapp(string $lastapp): self
    {
        $this->lastapp = $lastapp;

        return $this;
    }

    public function getLastdata(): ?string
    {
        return $this->lastdata;
    }

    public function setLastdata(string $lastdata): self
    {
        $this->lastdata = $lastdata;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getBillsec(): ?int
    {
        return $this->billsec;
    }

    public function setBillsec(int $billsec): self
    {
        $this->billsec = $billsec;

        return $this;
    }

    public function getDisposition(): ?string
    {
        return $this->disposition;
    }

    public function setDisposition(string $disposition): self
    {
        $this->disposition = $disposition;

        return $this;
    }

    public function getAmaflags(): ?int
    {
        return $this->amaflags;
    }

    public function setAmaflags(int $amaflags): self
    {
        $this->amaflags = $amaflags;

        return $this;
    }

    public function getAccountcode(): ?string
    {
        return $this->accountcode;
    }

    public function setAccountcode(string $accountcode): self
    {
        $this->accountcode = $accountcode;

        return $this;
    }

    public function getUniqueid(): ?string
    {
        return $this->uniqueid;
    }

    public function setUniqueid(string $uniqueid): self
    {
        $this->uniqueid = $uniqueid;

        return $this;
    }

    public function getUserfield(): ?string
    {
        return $this->userfield;
    }

    public function setUserfield(string $userfield): self
    {
        $this->userfield = $userfield;

        return $this;
    }

    public function getDid(): ?string
    {
        return $this->did;
    }

    public function setDid(string $did): self
    {
        $this->did = $did;

        return $this;
    }

    public function getScode(): ?string
    {
        return $this->scode;
    }

    public function setScode(string $scode): self
    {
        $this->scode = $scode;

        return $this;
    }

    public function getSrcOrg(): ?string
    {
        return $this->srcOrg;
    }

    public function setSrcOrg(string $srcOrg): self
    {
        $this->srcOrg = $srcOrg;

        return $this;
    }

    public function getDstOrg(): ?string
    {
        return $this->dstOrg;
    }

    public function setDstOrg(string $dstOrg): self
    {
        $this->dstOrg = $dstOrg;

        return $this;
    }


    public function getCallId(): ?string
    {
        return $this->callId;
    }


    public function setCallId(string $callId): self
    {
        $this->callId = $callId;
    }


}
