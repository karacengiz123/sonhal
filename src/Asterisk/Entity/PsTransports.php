<?php

namespace App\Asterisk\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * PsTransports
 * @ApiResource()
 * @ORM\Table(name="ps_transports",options={"collate"="utf8_general_ci"})
 * @ORM\Entity
 */
class PsTransports
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
     * @var int|null
     *
     * @ORM\Column(name="async_operations", type="integer", nullable=true)
     */
    private $asyncOperations;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bind", type="string", length=40, nullable=true)
     */
    private $bind;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ca_list_file", type="string", length=200, nullable=true)
     */
    private $caListFile;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cert_file", type="string", length=200, nullable=true)
     */
    private $certFile;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cipher", type="string", length=200, nullable=true)
     */
    private $cipher;

    /**
     * @var string|null
     *
     * @ORM\Column(name="domain", type="string", length=40, nullable=true)
     */
    private $domain;

    /**
     * @var string|null
     *
     * @ORM\Column(name="external_media_address", type="string", length=40, nullable=true)
     */
    private $externalMediaAddress;

    /**
     * @var string|null
     *
     * @ORM\Column(name="external_signaling_address", type="string", length=40, nullable=true)
     */
    private $externalSignalingAddress;

    /**
     * @var int|null
     *
     * @ORM\Column(name="external_signaling_port", type="integer", nullable=true)
     */
    private $externalSignalingPort;

    /**
     * @var string|null
     *
     * @ORM\Column(name="method", type="string", length=0, nullable=true)
     */
    private $method;

    /**
     * @var string|null
     *
     * @ORM\Column(name="local_net", type="string", length=40, nullable=true)
     */
    private $localNet;

    /**
     * @var string|null
     *
     * @ORM\Column(name="password", type="string", length=40, nullable=true)
     */
    private $password;

    /**
     * @var string|null
     *
     * @ORM\Column(name="priv_key_file", type="string", length=200, nullable=true)
     */
    private $privKeyFile;

    /**
     * @var string|null
     *
     * @ORM\Column(name="protocol", type="string", length=0, nullable=true)
     */
    private $protocol;

    /**
     * @var string|null
     *
     * @ORM\Column(name="require_client_cert", type="string", length=0, nullable=true)
     */
    private $requireClientCert;

    /**
     * @var string|null
     *
     * @ORM\Column(name="verify_client", type="string", length=0, nullable=true)
     */
    private $verifyClient;

    /**
     * @var string|null
     *
     * @ORM\Column(name="verify_server", type="string", length=0, nullable=true)
     */
    private $verifyServer;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tos", type="string", length=10, nullable=true)
     */
    private $tos;

    /**
     * @var int|null
     *
     * @ORM\Column(name="cos", type="integer", nullable=true)
     */
    private $cos;

    /**
     * @var string|null
     *
     * @ORM\Column(name="allow_reload", type="string", length=0, nullable=true)
     */
    private $allowReload;

    /**
     * @var string|null
     *
     * @ORM\Column(name="symmetric_transport", type="string", length=0, nullable=true)
     */
    private $symmetricTransport;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getAsyncOperations(): ?int
    {
        return $this->asyncOperations;
    }

    public function setAsyncOperations(?int $asyncOperations): self
    {
        $this->asyncOperations = $asyncOperations;

        return $this;
    }

    public function getBind(): ?string
    {
        return $this->bind;
    }

    public function setBind(?string $bind): self
    {
        $this->bind = $bind;

        return $this;
    }

    public function getCaListFile(): ?string
    {
        return $this->caListFile;
    }

    public function setCaListFile(?string $caListFile): self
    {
        $this->caListFile = $caListFile;

        return $this;
    }

    public function getCertFile(): ?string
    {
        return $this->certFile;
    }

    public function setCertFile(?string $certFile): self
    {
        $this->certFile = $certFile;

        return $this;
    }

    public function getCipher(): ?string
    {
        return $this->cipher;
    }

    public function setCipher(?string $cipher): self
    {
        $this->cipher = $cipher;

        return $this;
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

    public function getExternalMediaAddress(): ?string
    {
        return $this->externalMediaAddress;
    }

    public function setExternalMediaAddress(?string $externalMediaAddress): self
    {
        $this->externalMediaAddress = $externalMediaAddress;

        return $this;
    }

    public function getExternalSignalingAddress(): ?string
    {
        return $this->externalSignalingAddress;
    }

    public function setExternalSignalingAddress(?string $externalSignalingAddress): self
    {
        $this->externalSignalingAddress = $externalSignalingAddress;

        return $this;
    }

    public function getExternalSignalingPort(): ?int
    {
        return $this->externalSignalingPort;
    }

    public function setExternalSignalingPort(?int $externalSignalingPort): self
    {
        $this->externalSignalingPort = $externalSignalingPort;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(?string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getLocalNet(): ?string
    {
        return $this->localNet;
    }

    public function setLocalNet(?string $localNet): self
    {
        $this->localNet = $localNet;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPrivKeyFile(): ?string
    {
        return $this->privKeyFile;
    }

    public function setPrivKeyFile(?string $privKeyFile): self
    {
        $this->privKeyFile = $privKeyFile;

        return $this;
    }

    public function getProtocol(): ?string
    {
        return $this->protocol;
    }

    public function setProtocol(?string $protocol): self
    {
        $this->protocol = $protocol;

        return $this;
    }

    public function getRequireClientCert(): ?string
    {
        return $this->requireClientCert;
    }

    public function setRequireClientCert(?string $requireClientCert): self
    {
        $this->requireClientCert = $requireClientCert;

        return $this;
    }

    public function getVerifyClient(): ?string
    {
        return $this->verifyClient;
    }

    public function setVerifyClient(?string $verifyClient): self
    {
        $this->verifyClient = $verifyClient;

        return $this;
    }

    public function getVerifyServer(): ?string
    {
        return $this->verifyServer;
    }

    public function setVerifyServer(?string $verifyServer): self
    {
        $this->verifyServer = $verifyServer;

        return $this;
    }

    public function getTos(): ?string
    {
        return $this->tos;
    }

    public function setTos(?string $tos): self
    {
        $this->tos = $tos;

        return $this;
    }

    public function getCos(): ?int
    {
        return $this->cos;
    }

    public function setCos(?int $cos): self
    {
        $this->cos = $cos;

        return $this;
    }

    public function getAllowReload(): ?string
    {
        return $this->allowReload;
    }

    public function setAllowReload(?string $allowReload): self
    {
        $this->allowReload = $allowReload;

        return $this;
    }

    public function getSymmetricTransport(): ?string
    {
        return $this->symmetricTransport;
    }

    public function setSymmetricTransport(?string $symmetricTransport): self
    {
        $this->symmetricTransport = $symmetricTransport;

        return $this;
    }


}
