<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SmsSender;

/**
 * Description of SendingError
 *
 * @author Jarek
 */
class SendingError implements \SmsSender\SendingErrorInterface {

    private $code;
    private $message;
    private $recipient;
    
    public function __construct($recipient, $code, $message = '') {
        $this->code = (int)$code;
        $this->message = $message;
        $this->recipient = $recipient;
    }
    
    public function getCode() {
        return $this->code;
    }

    public function getMessage() {
        return $this->message;        
    }

    public function getRecipient() {
        return $this->recipient;
    }
}
