<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * PsEndpoints
 * @ApiResource()
 * @ORM\Table(name="ps_endpoints",options={"collate"="utf8_general_ci"})
 * @ORM\Entity
 */
class PsEndpoints
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
     * @ORM\Column(name="transport", type="string", length=40, nullable=true)
     */
    private $transport;

    /**
     * @var string|null
     *
     * @ORM\Column(name="aors", type="string", length=200, nullable=true)
     */
    private $aors;

    /**
     * @var string|null
     *
     * @ORM\Column(name="auth", type="string", length=40, nullable=true)
     */
    private $auth;

    /**
     * @var string|null
     *
     * @ORM\Column(name="context", type="string", length=40, nullable=true)
     */
    private $context;

    /**
     * @var string|null
     *
     * @ORM\Column(name="disallow", type="string", length=200, nullable=true)
     */
    private $disallow;

    /**
     * @var string|null
     *
     * @ORM\Column(name="allow", type="string", length=200, nullable=true)
     */
    private $allow;

    /**
     * @var string|null
     *
     * @ORM\Column(name="direct_media", type="string", length=0, nullable=true)
     */
    private $directMedia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="connected_line_method", type="string", length=0, nullable=true)
     */
    private $connectedLineMethod;

    /**
     * @var string|null
     *
     * @ORM\Column(name="direct_media_method", type="string", length=0, nullable=true)
     */
    private $directMediaMethod;

    /**
     * @var string|null
     *
     * @ORM\Column(name="direct_media_glare_mitigation", type="string", length=0, nullable=true)
     */
    private $directMediaGlareMitigation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="disable_direct_media_on_nat", type="string", length=0, nullable=true)
     */
    private $disableDirectMediaOnNat;

    /**
     * @var string|null
     *
     * @ORM\Column(name="dtmf_mode", type="string", length=0, nullable=true)
     */
    private $dtmfMode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="external_media_address", type="string", length=40, nullable=true)
     */
    private $externalMediaAddress;

    /**
     * @var string|null
     *
     * @ORM\Column(name="force_rport", type="string", length=0, nullable=true)
     */
    private $forceRport;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ice_support", type="string", length=0, nullable=true)
     */
    private $iceSupport;

    /**
     * @var string|null
     *
     * @ORM\Column(name="identify_by", type="string", length=0, nullable=true)
     */
    private $identifyBy;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mailboxes", type="string", length=40, nullable=true)
     */
    private $mailboxes;

    /**
     * @var string|null
     *
     * @ORM\Column(name="moh_suggest", type="string", length=40, nullable=true)
     */
    private $mohSuggest;

    /**
     * @var string|null
     *
     * @ORM\Column(name="outbound_auth", type="string", length=40, nullable=true)
     */
    private $outboundAuth;

    /**
     * @var string|null
     *
     * @ORM\Column(name="outbound_proxy", type="string", length=40, nullable=true)
     */
    private $outboundProxy;

    /**
     * @var string|null
     *
     * @ORM\Column(name="rewrite_contact", type="string", length=0, nullable=true)
     */
    private $rewriteContact;

    /**
     * @var string|null
     *
     * @ORM\Column(name="rtp_ipv6", type="string", length=0, nullable=true)
     */
    private $rtpIpv6;

    /**
     * @var string|null
     *
     * @ORM\Column(name="rtp_symmetric", type="string", length=0, nullable=true)
     */
    private $rtpSymmetric;

    /**
     * @var string|null
     *
     * @ORM\Column(name="send_diversion", type="string", length=0, nullable=true)
     */
    private $sendDiversion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="send_pai", type="string", length=0, nullable=true)
     */
    private $sendPai;

    /**
     * @var string|null
     *
     * @ORM\Column(name="send_rpid", type="string", length=0, nullable=true)
     */
    private $sendRpid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="timers_min_se", type="integer", nullable=true)
     */
    private $timersMinSe;

    /**
     * @var string|null
     *
     * @ORM\Column(name="timers", type="string", length=0, nullable=true)
     */
    private $timers;

    /**
     * @var int|null
     *
     * @ORM\Column(name="timers_sess_expires", type="integer", nullable=true)
     */
    private $timersSessExpires;

    /**
     * @var string|null
     *
     * @ORM\Column(name="callerid", type="string", length=40, nullable=true)
     */
    private $callerid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="callerid_privacy", type="string", length=0, nullable=true)
     */
    private $calleridPrivacy;

    /**
     * @var string|null
     *
     * @ORM\Column(name="callerid_tag", type="string", length=40, nullable=true)
     */
    private $calleridTag;

    /**
     * @var string|null
     *
     * @ORM\Column(name="100rel", type="string", length=0, nullable=true)
     */
    private $hundRedRel;

    /**
     * @var string|null
     *
     * @ORM\Column(name="aggregate_mwi", type="string", length=0, nullable=true)
     */
    private $aggregateMwi;

    /**
     * @var string|null
     *
     * @ORM\Column(name="trust_id_inbound", type="string", length=0, nullable=true)
     */
    private $trustIdInbound;

    /**
     * @var string|null
     *
     * @ORM\Column(name="trust_id_outbound", type="string", length=0, nullable=true)
     */
    private $trustIdOutbound;

    /**
     * @var string|null
     *
     * @ORM\Column(name="use_ptime", type="string", length=0, nullable=true)
     */
    private $usePtime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="use_avpf", type="string", length=0, nullable=true)
     */
    private $useAvpf;

    /**
     * @var string|null
     *
     * @ORM\Column(name="media_encryption", type="string", length=0, nullable=true)
     */
    private $mediaEncryption;

    /**
     * @var string|null
     *
     * @ORM\Column(name="inband_progress", type="string", length=0, nullable=true)
     */
    private $inbandProgress;

    /**
     * @var string|null
     *
     * @ORM\Column(name="call_group", type="string", length=40, nullable=true)
     */
    private $callGroup;

    /**
     * @var string|null
     *
     * @ORM\Column(name="pickup_group", type="string", length=40, nullable=true)
     */
    private $pickupGroup;

    /**
     * @var string|null
     *
     * @ORM\Column(name="named_call_group", type="string", length=40, nullable=true)
     */
    private $namedCallGroup;

    /**
     * @var string|null
     *
     * @ORM\Column(name="named_pickup_group", type="string", length=40, nullable=true)
     */
    private $namedPickupGroup;

    /**
     * @var int|null
     *
     * @ORM\Column(name="device_state_busy_at", type="integer", nullable=true)
     */
    private $deviceStateBusyAt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fax_detect", type="string", length=0, nullable=true)
     */
    private $faxDetect;

    /**
     * @var string|null
     *
     * @ORM\Column(name="t38_udptl", type="string", length=0, nullable=true)
     */
    private $t38Udptl;

    /**
     * @var string|null
     *
     * @ORM\Column(name="t38_udptl_ec", type="string", length=0, nullable=true)
     */
    private $t38UdptlEc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="t38_udptl_maxdatagram", type="integer", nullable=true)
     */
    private $t38UdptlMaxdatagram;

    /**
     * @var string|null
     *
     * @ORM\Column(name="t38_udptl_nat", type="string", length=0, nullable=true)
     */
    private $t38UdptlNat;

    /**
     * @var string|null
     *
     * @ORM\Column(name="t38_udptl_ipv6", type="string", length=0, nullable=true)
     */
    private $t38UdptlIpv6;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tone_zone", type="string", length=40, nullable=true)
     */
    private $toneZone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="language", type="string", length=40, nullable=true)
     */
    private $language;

    /**
     * @var string|null
     *
     * @ORM\Column(name="one_touch_recording", type="string", length=0, nullable=true)
     */
    private $oneTouchRecording;

    /**
     * @var string|null
     *
     * @ORM\Column(name="record_on_feature", type="string", length=40, nullable=true)
     */
    private $recordOnFeature;

    /**
     * @var string|null
     *
     * @ORM\Column(name="record_off_feature", type="string", length=40, nullable=true)
     */
    private $recordOffFeature;

    /**
     * @var string|null
     *
     * @ORM\Column(name="rtp_engine", type="string", length=40, nullable=true)
     */
    private $rtpEngine;

    /**
     * @var string|null
     *
     * @ORM\Column(name="allow_transfer", type="string", length=0, nullable=true)
     */
    private $allowTransfer;

    /**
     * @var string|null
     *
     * @ORM\Column(name="allow_subscribe", type="string", length=0, nullable=true)
     */
    private $allowSubscribe;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sdp_owner", type="string", length=40, nullable=true)
     */
    private $sdpOwner;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sdp_session", type="string", length=40, nullable=true)
     */
    private $sdpSession;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tos_audio", type="string", length=10, nullable=true)
     */
    private $tosAudio;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tos_video", type="string", length=10, nullable=true)
     */
    private $tosVideo;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sub_min_expiry", type="integer", nullable=true)
     */
    private $subMinExpiry;

    /**
     * @var string|null
     *
     * @ORM\Column(name="from_domain", type="string", length=40, nullable=true)
     */
    private $fromDomain;

    /**
     * @var string|null
     *
     * @ORM\Column(name="from_user", type="string", length=40, nullable=true)
     */
    private $fromUser;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mwi_from_user", type="string", length=40, nullable=true)
     */
    private $mwiFromUser;

    /**
     * @var string|null
     *
     * @ORM\Column(name="dtls_verify", type="string", length=40, nullable=true)
     */
    private $dtlsVerify;

    /**
     * @var string|null
     *
     * @ORM\Column(name="dtls_rekey", type="string", length=40, nullable=true)
     */
    private $dtlsRekey;

    /**
     * @var string|null
     *
     * @ORM\Column(name="dtls_cert_file", type="string", length=200, nullable=true)
     */
    private $dtlsCertFile;

    /**
     * @var string|null
     *
     * @ORM\Column(name="dtls_private_key", type="string", length=200, nullable=true)
     */
    private $dtlsPrivateKey;

    /**
     * @var string|null
     *
     * @ORM\Column(name="dtls_cipher", type="string", length=200, nullable=true)
     */
    private $dtlsCipher;

    /**
     * @var string|null
     *
     * @ORM\Column(name="dtls_ca_file", type="string", length=200, nullable=true)
     */
    private $dtlsCaFile;

    /**
     * @var string|null
     *
     * @ORM\Column(name="dtls_ca_path", type="string", length=200, nullable=true)
     */
    private $dtlsCaPath;

    /**
     * @var string|null
     *
     * @ORM\Column(name="dtls_setup", type="string", length=0, nullable=true)
     */
    private $dtlsSetup;

    /**
     * @var string|null
     *
     * @ORM\Column(name="srtp_tag_32", type="string", length=0, nullable=true)
     */
    private $srtpTag32;

    /**
     * @var string|null
     *
     * @ORM\Column(name="media_address", type="string", length=40, nullable=true)
     */
    private $mediaAddress;

    /**
     * @var string|null
     *
     * @ORM\Column(name="redirect_method", type="string", length=0, nullable=true)
     */
    private $redirectMethod;

    /**
     * @var string|null
     *
     * @ORM\Column(name="set_var", type="text", length=65535, nullable=true)
     */
    private $setVar;

    /**
     * @var int|null
     *
     * @ORM\Column(name="cos_audio", type="integer", nullable=true)
     */
    private $cosAudio;

    /**
     * @var int|null
     *
     * @ORM\Column(name="cos_video", type="integer", nullable=true)
     */
    private $cosVideo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="message_context", type="string", length=40, nullable=true)
     */
    private $messageContext;

    /**
     * @var string|null
     *
     * @ORM\Column(name="force_avp", type="string", length=0, nullable=true)
     */
    private $forceAvp;

    /**
     * @var string|null
     *
     * @ORM\Column(name="media_use_received_transport", type="string", length=0, nullable=true)
     */
    private $mediaUseReceivedTransport;

    /**
     * @var string|null
     *
     * @ORM\Column(name="accountcode", type="string", length=80, nullable=true)
     */
    private $accountcode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_eq_phone", type="string", length=0, nullable=true)
     */
    private $userEqPhone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="moh_passthrough", type="string", length=0, nullable=true)
     */
    private $mohPassthrough;

    /**
     * @var string|null
     *
     * @ORM\Column(name="media_encryption_optimistic", type="string", length=0, nullable=true)
     */
    private $mediaEncryptionOptimistic;

    /**
     * @var string|null
     *
     * @ORM\Column(name="rpid_immediate", type="string", length=0, nullable=true)
     */
    private $rpidImmediate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="g726_non_standard", type="string", length=0, nullable=true)
     */
    private $g726NonStandard;

    /**
     * @var int|null
     *
     * @ORM\Column(name="rtp_keepalive", type="integer", nullable=true)
     */
    private $rtpKeepalive;

    /**
     * @var int|null
     *
     * @ORM\Column(name="rtp_timeout", type="integer", nullable=true)
     */
    private $rtpTimeout;

    /**
     * @var int|null
     *
     * @ORM\Column(name="rtp_timeout_hold", type="integer", nullable=true)
     */
    private $rtpTimeoutHold;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bind_rtp_to_media_address", type="string", length=0, nullable=true)
     */
    private $bindRtpToMediaAddress;

    /**
     * @var string|null
     *
     * @ORM\Column(name="voicemail_extension", type="string", length=40, nullable=true)
     */
    private $voicemailExtension;

    /**
     * @var int|null
     *
     * @ORM\Column(name="mwi_subscribe_replaces_unsolicited", type="integer", nullable=true)
     */
    private $mwiSubscribeReplacesUnsolicited;

    /**
     * @var string|null
     *
     * @ORM\Column(name="deny", type="string", length=95, nullable=true)
     */
    private $deny;

    /**
     * @var string|null
     *
     * @ORM\Column(name="permit", type="string", length=95, nullable=true)
     */
    private $permit;

    /**
     * @var string|null
     *
     * @ORM\Column(name="acl", type="string", length=40, nullable=true)
     */
    private $acl;

    /**
     * @var string|null
     *
     * @ORM\Column(name="contact_deny", type="string", length=95, nullable=true)
     */
    private $contactDeny;

    /**
     * @var string|null
     *
     * @ORM\Column(name="contact_permit", type="string", length=95, nullable=true)
     */
    private $contactPermit;

    /**
     * @var string|null
     *
     * @ORM\Column(name="contact_acl", type="string", length=40, nullable=true)
     */
    private $contactAcl;

    /**
     * @var string|null
     *
     * @ORM\Column(name="subscribe_context", type="string", length=40, nullable=true)
     */
    private $subscribeContext;

    /**
     * @var int|null
     *
     * @ORM\Column(name="fax_detect_timeout", type="integer", nullable=true)
     */
    private $faxDetectTimeout;

    /**
     * @var string|null
     *
     * @ORM\Column(name="contact_user", type="string", length=80, nullable=true)
     */
    private $contactUser;

    /**
     * @var string|null
     *
     * @ORM\Column(name="preferred_codec_only", type="string", length=0, nullable=true)
     */
    private $preferredCodecOnly;

    /**
     * @var string|null
     *
     * @ORM\Column(name="asymmetric_rtp_codec", type="string", length=0, nullable=true)
     */
    private $asymmetricRtpCodec;

    /**
     * @var string|null
     *
     * @ORM\Column(name="rtcp_mux", type="string", length=0, nullable=true)
     */
    private $rtcpMux;

    /**
     * @var string|null
     *
     * @ORM\Column(name="allow_overlap", type="string", length=0, nullable=true)
     */
    private $allowOverlap;

    /**
     * @var string|null
     *
     * @ORM\Column(name="refer_blind_progress", type="string", length=0, nullable=true)
     */
    private $referBlindProgress;

    /**
     * @var string|null
     *
     * @ORM\Column(name="notify_early_inuse_ringing", type="string", length=0, nullable=true)
     */
    private $notifyEarlyInuseRinging;

    /**
     * @var int|null
     *
     * @ORM\Column(name="max_audio_streams", type="integer", nullable=true)
     */
    private $maxAudioStreams;

    /**
     * @var int|null
     *
     * @ORM\Column(name="max_video_streams", type="integer", nullable=true)
     */
    private $maxVideoStreams;

    /**
     * @var string|null
     *
     * @ORM\Column(name="webrtc", type="string", length=0, nullable=true)
     */
    private $webrtc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="dtls_fingerprint", type="string", length=0, nullable=true)
     */
    private $dtlsFingerprint;

    /**
     * @var string|null
     *
     * @ORM\Column(name="incoming_mwi_mailbox", type="string", length=40, nullable=true)
     */
    private $incomingMwiMailbox;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTransport(): ?string
    {
        return $this->transport;
    }

    public function setTransport(?string $transport): self
    {
        $this->transport = $transport;

        return $this;
    }

    public function getAors(): ?string
    {
        return $this->aors;
    }

    public function setAors(?string $aors): self
    {
        $this->aors = $aors;

        return $this;
    }

    public function getAuth(): ?string
    {
        return $this->auth;
    }

    public function setAuth(?string $auth): self
    {
        $this->auth = $auth;

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

    public function getDisallow(): ?string
    {
        return $this->disallow;
    }

    public function setDisallow(?string $disallow): self
    {
        $this->disallow = $disallow;

        return $this;
    }

    public function getAllow(): ?string
    {
        return $this->allow;
    }

    public function setAllow(?string $allow): self
    {
        $this->allow = $allow;

        return $this;
    }

    public function getDirectMedia(): ?string
    {
        return $this->directMedia;
    }

    public function setDirectMedia(?string $directMedia): self
    {
        $this->directMedia = $directMedia;

        return $this;
    }

    public function getConnectedLineMethod(): ?string
    {
        return $this->connectedLineMethod;
    }

    public function setConnectedLineMethod(?string $connectedLineMethod): self
    {
        $this->connectedLineMethod = $connectedLineMethod;

        return $this;
    }

    public function getDirectMediaMethod(): ?string
    {
        return $this->directMediaMethod;
    }

    public function setDirectMediaMethod(?string $directMediaMethod): self
    {
        $this->directMediaMethod = $directMediaMethod;

        return $this;
    }

    public function getDirectMediaGlareMitigation(): ?string
    {
        return $this->directMediaGlareMitigation;
    }

    public function setDirectMediaGlareMitigation(?string $directMediaGlareMitigation): self
    {
        $this->directMediaGlareMitigation = $directMediaGlareMitigation;

        return $this;
    }

    public function getDisableDirectMediaOnNat(): ?string
    {
        return $this->disableDirectMediaOnNat;
    }

    public function setDisableDirectMediaOnNat(?string $disableDirectMediaOnNat): self
    {
        $this->disableDirectMediaOnNat = $disableDirectMediaOnNat;

        return $this;
    }

    public function getDtmfMode(): ?string
    {
        return $this->dtmfMode;
    }

    public function setDtmfMode(?string $dtmfMode): self
    {
        $this->dtmfMode = $dtmfMode;

        return $this;
    }

    public function getExternalMediaAddress(): ?string
    {
        return $this->externalMediaAddress;
    }

    public function setExternalMediaAddress(?string $externalMediaAddress): self
    {
        $this->externalMediaAddress = $externalMediaAddress;

        return $this;
    }

    public function getForceRport(): ?string
    {
        return $this->forceRport;
    }

    public function setForceRport(?string $forceRport): self
    {
        $this->forceRport = $forceRport;

        return $this;
    }

    public function getIceSupport(): ?string
    {
        return $this->iceSupport;
    }

    public function setIceSupport(?string $iceSupport): self
    {
        $this->iceSupport = $iceSupport;

        return $this;
    }

    public function getIdentifyBy(): ?string
    {
        return $this->identifyBy;
    }

    public function setIdentifyBy(?string $identifyBy): self
    {
        $this->identifyBy = $identifyBy;

        return $this;
    }

    public function getMailboxes(): ?string
    {
        return $this->mailboxes;
    }

    public function setMailboxes(?string $mailboxes): self
    {
        $this->mailboxes = $mailboxes;

        return $this;
    }

    public function getMohSuggest(): ?string
    {
        return $this->mohSuggest;
    }

    public function setMohSuggest(?string $mohSuggest): self
    {
        $this->mohSuggest = $mohSuggest;

        return $this;
    }

    public function getOutboundAuth(): ?string
    {
        return $this->outboundAuth;
    }

    public function setOutboundAuth(?string $outboundAuth): self
    {
        $this->outboundAuth = $outboundAuth;

        return $this;
    }

    public function getOutboundProxy(): ?string
    {
        return $this->outboundProxy;
    }

    public function setOutboundProxy(?string $outboundProxy): self
    {
        $this->outboundProxy = $outboundProxy;

        return $this;
    }

    public function getRewriteContact(): ?string
    {
        return $this->rewriteContact;
    }

    public function setRewriteContact(?string $rewriteContact): self
    {
        $this->rewriteContact = $rewriteContact;

        return $this;
    }

    public function getRtpIpv6(): ?string
    {
        return $this->rtpIpv6;
    }

    public function setRtpIpv6(?string $rtpIpv6): self
    {
        $this->rtpIpv6 = $rtpIpv6;

        return $this;
    }

    public function getRtpSymmetric(): ?string
    {
        return $this->rtpSymmetric;
    }

    public function setRtpSymmetric(?string $rtpSymmetric): self
    {
        $this->rtpSymmetric = $rtpSymmetric;

        return $this;
    }

    public function getSendDiversion(): ?string
    {
        return $this->sendDiversion;
    }

    public function setSendDiversion(?string $sendDiversion): self
    {
        $this->sendDiversion = $sendDiversion;

        return $this;
    }

    public function getSendPai(): ?string
    {
        return $this->sendPai;
    }

    public function setSendPai(?string $sendPai): self
    {
        $this->sendPai = $sendPai;

        return $this;
    }

    public function getSendRpid(): ?string
    {
        return $this->sendRpid;
    }

    public function setSendRpid(?string $sendRpid): self
    {
        $this->sendRpid = $sendRpid;

        return $this;
    }

    public function getTimersMinSe(): ?int
    {
        return $this->timersMinSe;
    }

    public function setTimersMinSe(?int $timersMinSe): self
    {
        $this->timersMinSe = $timersMinSe;

        return $this;
    }

    public function getTimers(): ?string
    {
        return $this->timers;
    }

    public function setTimers(?string $timers): self
    {
        $this->timers = $timers;

        return $this;
    }

    public function getTimersSessExpires(): ?int
    {
        return $this->timersSessExpires;
    }

    public function setTimersSessExpires(?int $timersSessExpires): self
    {
        $this->timersSessExpires = $timersSessExpires;

        return $this;
    }

    public function getCallerid(): ?string
    {
        return $this->callerid;
    }

    public function setCallerid(?string $callerid): self
    {
        $this->callerid = $callerid;

        return $this;
    }

    public function getCalleridPrivacy(): ?string
    {
        return $this->calleridPrivacy;
    }

    public function setCalleridPrivacy(?string $calleridPrivacy): self
    {
        $this->calleridPrivacy = $calleridPrivacy;

        return $this;
    }

    public function getCalleridTag(): ?string
    {
        return $this->calleridTag;
    }

    public function setCalleridTag(?string $calleridTag): self
    {
        $this->calleridTag = $calleridTag;

        return $this;
    }

    public function getHundRedRel(): ?string
    {
        return $this->hundRedRel;
    }

    public function setHundRedRel(?string $hundRedRel): self
    {
        $this->hundRedRel = $hundRedRel;

        return $this;
    }

    public function getAggregateMwi(): ?string
    {
        return $this->aggregateMwi;
    }

    public function setAggregateMwi(?string $aggregateMwi): self
    {
        $this->aggregateMwi = $aggregateMwi;

        return $this;
    }

    public function getTrustIdInbound(): ?string
    {
        return $this->trustIdInbound;
    }

    public function setTrustIdInbound(?string $trustIdInbound): self
    {
        $this->trustIdInbound = $trustIdInbound;

        return $this;
    }

    public function getTrustIdOutbound(): ?string
    {
        return $this->trustIdOutbound;
    }

    public function setTrustIdOutbound(?string $trustIdOutbound): self
    {
        $this->trustIdOutbound = $trustIdOutbound;

        return $this;
    }

    public function getUsePtime(): ?string
    {
        return $this->usePtime;
    }

    public function setUsePtime(?string $usePtime): self
    {
        $this->usePtime = $usePtime;

        return $this;
    }

    public function getUseAvpf(): ?string
    {
        return $this->useAvpf;
    }

    public function setUseAvpf(?string $useAvpf): self
    {
        $this->useAvpf = $useAvpf;

        return $this;
    }

    public function getMediaEncryption(): ?string
    {
        return $this->mediaEncryption;
    }

    public function setMediaEncryption(?string $mediaEncryption): self
    {
        $this->mediaEncryption = $mediaEncryption;

        return $this;
    }

    public function getInbandProgress(): ?string
    {
        return $this->inbandProgress;
    }

    public function setInbandProgress(?string $inbandProgress): self
    {
        $this->inbandProgress = $inbandProgress;

        return $this;
    }

    public function getCallGroup(): ?string
    {
        return $this->callGroup;
    }

    public function setCallGroup(?string $callGroup): self
    {
        $this->callGroup = $callGroup;

        return $this;
    }

    public function getPickupGroup(): ?string
    {
        return $this->pickupGroup;
    }

    public function setPickupGroup(?string $pickupGroup): self
    {
        $this->pickupGroup = $pickupGroup;

        return $this;
    }

    public function getNamedCallGroup(): ?string
    {
        return $this->namedCallGroup;
    }

    public function setNamedCallGroup(?string $namedCallGroup): self
    {
        $this->namedCallGroup = $namedCallGroup;

        return $this;
    }

    public function getNamedPickupGroup(): ?string
    {
        return $this->namedPickupGroup;
    }

    public function setNamedPickupGroup(?string $namedPickupGroup): self
    {
        $this->namedPickupGroup = $namedPickupGroup;

        return $this;
    }

    public function getDeviceStateBusyAt(): ?int
    {
        return $this->deviceStateBusyAt;
    }

    public function setDeviceStateBusyAt(?int $deviceStateBusyAt): self
    {
        $this->deviceStateBusyAt = $deviceStateBusyAt;

        return $this;
    }

    public function getFaxDetect(): ?string
    {
        return $this->faxDetect;
    }

    public function setFaxDetect(?string $faxDetect): self
    {
        $this->faxDetect = $faxDetect;

        return $this;
    }

    public function getT38Udptl(): ?string
    {
        return $this->t38Udptl;
    }

    public function setT38Udptl(?string $t38Udptl): self
    {
        $this->t38Udptl = $t38Udptl;

        return $this;
    }

    public function getT38UdptlEc(): ?string
    {
        return $this->t38UdptlEc;
    }

    public function setT38UdptlEc(?string $t38UdptlEc): self
    {
        $this->t38UdptlEc = $t38UdptlEc;

        return $this;
    }

    public function getT38UdptlMaxdatagram(): ?int
    {
        return $this->t38UdptlMaxdatagram;
    }

    public function setT38UdptlMaxdatagram(?int $t38UdptlMaxdatagram): self
    {
        $this->t38UdptlMaxdatagram = $t38UdptlMaxdatagram;

        return $this;
    }

    public function getT38UdptlNat(): ?string
    {
        return $this->t38UdptlNat;
    }

    public function setT38UdptlNat(?string $t38UdptlNat): self
    {
        $this->t38UdptlNat = $t38UdptlNat;

        return $this;
    }

    public function getT38UdptlIpv6(): ?string
    {
        return $this->t38UdptlIpv6;
    }

    public function setT38UdptlIpv6(?string $t38UdptlIpv6): self
    {
        $this->t38UdptlIpv6 = $t38UdptlIpv6;

        return $this;
    }

    public function getToneZone(): ?string
    {
        return $this->toneZone;
    }

    public function setToneZone(?string $toneZone): self
    {
        $this->toneZone = $toneZone;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getOneTouchRecording(): ?string
    {
        return $this->oneTouchRecording;
    }

    public function setOneTouchRecording(?string $oneTouchRecording): self
    {
        $this->oneTouchRecording = $oneTouchRecording;

        return $this;
    }

    public function getRecordOnFeature(): ?string
    {
        return $this->recordOnFeature;
    }

    public function setRecordOnFeature(?string $recordOnFeature): self
    {
        $this->recordOnFeature = $recordOnFeature;

        return $this;
    }

    public function getRecordOffFeature(): ?string
    {
        return $this->recordOffFeature;
    }

    public function setRecordOffFeature(?string $recordOffFeature): self
    {
        $this->recordOffFeature = $recordOffFeature;

        return $this;
    }

    public function getRtpEngine(): ?string
    {
        return $this->rtpEngine;
    }

    public function setRtpEngine(?string $rtpEngine): self
    {
        $this->rtpEngine = $rtpEngine;

        return $this;
    }

    public function getAllowTransfer(): ?string
    {
        return $this->allowTransfer;
    }

    public function setAllowTransfer(?string $allowTransfer): self
    {
        $this->allowTransfer = $allowTransfer;

        return $this;
    }

    public function getAllowSubscribe(): ?string
    {
        return $this->allowSubscribe;
    }

    public function setAllowSubscribe(?string $allowSubscribe): self
    {
        $this->allowSubscribe = $allowSubscribe;

        return $this;
    }

    public function getSdpOwner(): ?string
    {
        return $this->sdpOwner;
    }

    public function setSdpOwner(?string $sdpOwner): self
    {
        $this->sdpOwner = $sdpOwner;

        return $this;
    }

    public function getSdpSession(): ?string
    {
        return $this->sdpSession;
    }

    public function setSdpSession(?string $sdpSession): self
    {
        $this->sdpSession = $sdpSession;

        return $this;
    }

    public function getTosAudio(): ?string
    {
        return $this->tosAudio;
    }

    public function setTosAudio(?string $tosAudio): self
    {
        $this->tosAudio = $tosAudio;

        return $this;
    }

    public function getTosVideo(): ?string
    {
        return $this->tosVideo;
    }

    public function setTosVideo(?string $tosVideo): self
    {
        $this->tosVideo = $tosVideo;

        return $this;
    }

    public function getSubMinExpiry(): ?int
    {
        return $this->subMinExpiry;
    }

    public function setSubMinExpiry(?int $subMinExpiry): self
    {
        $this->subMinExpiry = $subMinExpiry;

        return $this;
    }

    public function getFromDomain(): ?string
    {
        return $this->fromDomain;
    }

    public function setFromDomain(?string $fromDomain): self
    {
        $this->fromDomain = $fromDomain;

        return $this;
    }

    public function getFromUser(): ?string
    {
        return $this->fromUser;
    }

    public function setFromUser(?string $fromUser): self
    {
        $this->fromUser = $fromUser;

        return $this;
    }

    public function getMwiFromUser(): ?string
    {
        return $this->mwiFromUser;
    }

    public function setMwiFromUser(?string $mwiFromUser): self
    {
        $this->mwiFromUser = $mwiFromUser;

        return $this;
    }

    public function getDtlsVerify(): ?string
    {
        return $this->dtlsVerify;
    }

    public function setDtlsVerify(?string $dtlsVerify): self
    {
        $this->dtlsVerify = $dtlsVerify;

        return $this;
    }

    public function getDtlsRekey(): ?string
    {
        return $this->dtlsRekey;
    }

    public function setDtlsRekey(?string $dtlsRekey): self
    {
        $this->dtlsRekey = $dtlsRekey;

        return $this;
    }

    public function getDtlsCertFile(): ?string
    {
        return $this->dtlsCertFile;
    }

    public function setDtlsCertFile(?string $dtlsCertFile): self
    {
        $this->dtlsCertFile = $dtlsCertFile;

        return $this;
    }

    public function getDtlsPrivateKey(): ?string
    {
        return $this->dtlsPrivateKey;
    }

    public function setDtlsPrivateKey(?string $dtlsPrivateKey): self
    {
        $this->dtlsPrivateKey = $dtlsPrivateKey;

        return $this;
    }

    public function getDtlsCipher(): ?string
    {
        return $this->dtlsCipher;
    }

    public function setDtlsCipher(?string $dtlsCipher): self
    {
        $this->dtlsCipher = $dtlsCipher;

        return $this;
    }

    public function getDtlsCaFile(): ?string
    {
        return $this->dtlsCaFile;
    }

    public function setDtlsCaFile(?string $dtlsCaFile): self
    {
        $this->dtlsCaFile = $dtlsCaFile;

        return $this;
    }

    public function getDtlsCaPath(): ?string
    {
        return $this->dtlsCaPath;
    }

    public function setDtlsCaPath(?string $dtlsCaPath): self
    {
        $this->dtlsCaPath = $dtlsCaPath;

        return $this;
    }

    public function getDtlsSetup(): ?string
    {
        return $this->dtlsSetup;
    }

    public function setDtlsSetup(?string $dtlsSetup): self
    {
        $this->dtlsSetup = $dtlsSetup;

        return $this;
    }

    public function getSrtpTag32(): ?string
    {
        return $this->srtpTag32;
    }

    public function setSrtpTag32(?string $srtpTag32): self
    {
        $this->srtpTag32 = $srtpTag32;

        return $this;
    }

    public function getMediaAddress(): ?string
    {
        return $this->mediaAddress;
    }

    public function setMediaAddress(?string $mediaAddress): self
    {
        $this->mediaAddress = $mediaAddress;

        return $this;
    }

    public function getRedirectMethod(): ?string
    {
        return $this->redirectMethod;
    }

    public function setRedirectMethod(?string $redirectMethod): self
    {
        $this->redirectMethod = $redirectMethod;

        return $this;
    }

    public function getSetVar(): ?string
    {
        return $this->setVar;
    }

    public function setSetVar(?string $setVar): self
    {
        $this->setVar = $setVar;

        return $this;
    }

    public function getCosAudio(): ?int
    {
        return $this->cosAudio;
    }

    public function setCosAudio(?int $cosAudio): self
    {
        $this->cosAudio = $cosAudio;

        return $this;
    }

    public function getCosVideo(): ?int
    {
        return $this->cosVideo;
    }

    public function setCosVideo(?int $cosVideo): self
    {
        $this->cosVideo = $cosVideo;

        return $this;
    }

    public function getMessageContext(): ?string
    {
        return $this->messageContext;
    }

    public function setMessageContext(?string $messageContext): self
    {
        $this->messageContext = $messageContext;

        return $this;
    }

    public function getForceAvp(): ?string
    {
        return $this->forceAvp;
    }

    public function setForceAvp(?string $forceAvp): self
    {
        $this->forceAvp = $forceAvp;

        return $this;
    }

    public function getMediaUseReceivedTransport(): ?string
    {
        return $this->mediaUseReceivedTransport;
    }

    public function setMediaUseReceivedTransport(?string $mediaUseReceivedTransport): self
    {
        $this->mediaUseReceivedTransport = $mediaUseReceivedTransport;

        return $this;
    }

    public function getAccountcode(): ?string
    {
        return $this->accountcode;
    }

    public function setAccountcode(?string $accountcode): self
    {
        $this->accountcode = $accountcode;

        return $this;
    }

    public function getUserEqPhone(): ?string
    {
        return $this->userEqPhone;
    }

    public function setUserEqPhone(?string $userEqPhone): self
    {
        $this->userEqPhone = $userEqPhone;

        return $this;
    }

    public function getMohPassthrough(): ?string
    {
        return $this->mohPassthrough;
    }

    public function setMohPassthrough(?string $mohPassthrough): self
    {
        $this->mohPassthrough = $mohPassthrough;

        return $this;
    }

    public function getMediaEncryptionOptimistic(): ?string
    {
        return $this->mediaEncryptionOptimistic;
    }

    public function setMediaEncryptionOptimistic(?string $mediaEncryptionOptimistic): self
    {
        $this->mediaEncryptionOptimistic = $mediaEncryptionOptimistic;

        return $this;
    }

    public function getRpidImmediate(): ?string
    {
        return $this->rpidImmediate;
    }

    public function setRpidImmediate(?string $rpidImmediate): self
    {
        $this->rpidImmediate = $rpidImmediate;

        return $this;
    }

    public function getG726NonStandard(): ?string
    {
        return $this->g726NonStandard;
    }

    public function setG726NonStandard(?string $g726NonStandard): self
    {
        $this->g726NonStandard = $g726NonStandard;

        return $this;
    }

    public function getRtpKeepalive(): ?int
    {
        return $this->rtpKeepalive;
    }

    public function setRtpKeepalive(?int $rtpKeepalive): self
    {
        $this->rtpKeepalive = $rtpKeepalive;

        return $this;
    }

    public function getRtpTimeout(): ?int
    {
        return $this->rtpTimeout;
    }

    public function setRtpTimeout(?int $rtpTimeout): self
    {
        $this->rtpTimeout = $rtpTimeout;

        return $this;
    }

    public function getRtpTimeoutHold(): ?int
    {
        return $this->rtpTimeoutHold;
    }

    public function setRtpTimeoutHold(?int $rtpTimeoutHold): self
    {
        $this->rtpTimeoutHold = $rtpTimeoutHold;

        return $this;
    }

    public function getBindRtpToMediaAddress(): ?string
    {
        return $this->bindRtpToMediaAddress;
    }

    public function setBindRtpToMediaAddress(?string $bindRtpToMediaAddress): self
    {
        $this->bindRtpToMediaAddress = $bindRtpToMediaAddress;

        return $this;
    }

    public function getVoicemailExtension(): ?string
    {
        return $this->voicemailExtension;
    }

    public function setVoicemailExtension(?string $voicemailExtension): self
    {
        $this->voicemailExtension = $voicemailExtension;

        return $this;
    }

    public function getMwiSubscribeReplacesUnsolicited(): ?int
    {
        return $this->mwiSubscribeReplacesUnsolicited;
    }

    public function setMwiSubscribeReplacesUnsolicited(?int $mwiSubscribeReplacesUnsolicited): self
    {
        $this->mwiSubscribeReplacesUnsolicited = $mwiSubscribeReplacesUnsolicited;

        return $this;
    }

    public function getDeny(): ?string
    {
        return $this->deny;
    }

    public function setDeny(?string $deny): self
    {
        $this->deny = $deny;

        return $this;
    }

    public function getPermit(): ?string
    {
        return $this->permit;
    }

    public function setPermit(?string $permit): self
    {
        $this->permit = $permit;

        return $this;
    }

    public function getAcl(): ?string
    {
        return $this->acl;
    }

    public function setAcl(?string $acl): self
    {
        $this->acl = $acl;

        return $this;
    }

    public function getContactDeny(): ?string
    {
        return $this->contactDeny;
    }

    public function setContactDeny(?string $contactDeny): self
    {
        $this->contactDeny = $contactDeny;

        return $this;
    }

    public function getContactPermit(): ?string
    {
        return $this->contactPermit;
    }

    public function setContactPermit(?string $contactPermit): self
    {
        $this->contactPermit = $contactPermit;

        return $this;
    }

    public function getContactAcl(): ?string
    {
        return $this->contactAcl;
    }

    public function setContactAcl(?string $contactAcl): self
    {
        $this->contactAcl = $contactAcl;

        return $this;
    }

    public function getSubscribeContext(): ?string
    {
        return $this->subscribeContext;
    }

    public function setSubscribeContext(?string $subscribeContext): self
    {
        $this->subscribeContext = $subscribeContext;

        return $this;
    }

    public function getFaxDetectTimeout(): ?int
    {
        return $this->faxDetectTimeout;
    }

    public function setFaxDetectTimeout(?int $faxDetectTimeout): self
    {
        $this->faxDetectTimeout = $faxDetectTimeout;

        return $this;
    }

    public function getContactUser(): ?string
    {
        return $this->contactUser;
    }

    public function setContactUser(?string $contactUser): self
    {
        $this->contactUser = $contactUser;

        return $this;
    }

    public function getPreferredCodecOnly(): ?string
    {
        return $this->preferredCodecOnly;
    }

    public function setPreferredCodecOnly(?string $preferredCodecOnly): self
    {
        $this->preferredCodecOnly = $preferredCodecOnly;

        return $this;
    }

    public function getAsymmetricRtpCodec(): ?string
    {
        return $this->asymmetricRtpCodec;
    }

    public function setAsymmetricRtpCodec(?string $asymmetricRtpCodec): self
    {
        $this->asymmetricRtpCodec = $asymmetricRtpCodec;

        return $this;
    }

    public function getRtcpMux(): ?string
    {
        return $this->rtcpMux;
    }

    public function setRtcpMux(?string $rtcpMux): self
    {
        $this->rtcpMux = $rtcpMux;

        return $this;
    }

    public function getAllowOverlap(): ?string
    {
        return $this->allowOverlap;
    }

    public function setAllowOverlap(?string $allowOverlap): self
    {
        $this->allowOverlap = $allowOverlap;

        return $this;
    }

    public function getReferBlindProgress(): ?string
    {
        return $this->referBlindProgress;
    }

    public function setReferBlindProgress(?string $referBlindProgress): self
    {
        $this->referBlindProgress = $referBlindProgress;

        return $this;
    }

    public function getNotifyEarlyInuseRinging(): ?string
    {
        return $this->notifyEarlyInuseRinging;
    }

    public function setNotifyEarlyInuseRinging(?string $notifyEarlyInuseRinging): self
    {
        $this->notifyEarlyInuseRinging = $notifyEarlyInuseRinging;

        return $this;
    }

    public function getMaxAudioStreams(): ?int
    {
        return $this->maxAudioStreams;
    }

    public function setMaxAudioStreams(?int $maxAudioStreams): self
    {
        $this->maxAudioStreams = $maxAudioStreams;

        return $this;
    }

    public function getMaxVideoStreams(): ?int
    {
        return $this->maxVideoStreams;
    }

    public function setMaxVideoStreams(?int $maxVideoStreams): self
    {
        $this->maxVideoStreams = $maxVideoStreams;

        return $this;
    }

    public function getWebrtc(): ?string
    {
        return $this->webrtc;
    }

    public function setWebrtc(?string $webrtc): self
    {
        $this->webrtc = $webrtc;

        return $this;
    }

    public function getDtlsFingerprint(): ?string
    {
        return $this->dtlsFingerprint;
    }

    public function setDtlsFingerprint(?string $dtlsFingerprint): self
    {
        $this->dtlsFingerprint = $dtlsFingerprint;

        return $this;
    }

    public function getIncomingMwiMailbox(): ?string
    {
        return $this->incomingMwiMailbox;
    }

    public function setIncomingMwiMailbox(?string $incomingMwiMailbox): self
    {
        $this->incomingMwiMailbox = $incomingMwiMailbox;

        return $this;
    }


}
