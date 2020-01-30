<?php


namespace App\AsteriskAction;


use PAMI\Message\Action\ActionMessage;

class PJSIPShowEndpointAction extends  ActionMessage
{
    public function __construct($exten)
    {
        parent::__construct('PJSIPShowEndpoint');
        $this->setKey('Endpoint', $exten);
    }

}