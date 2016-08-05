<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SmsSender\Adapter;

use SmsSender\MessageInterface;

/**
 * Description of MockGateway
 *
 * @author Jarek
 */
class MockAdapter extends AbstractAdapter {
    
    /**
     * @var array Sent messages
     */
    private $sentMessages = array();
    
    public function send(MessageInterface $message, $skipErrors = true) {
        $this->sentMessages[] = $message;
        return true;
    }

    /**
     * @return array
     */
    public function getSentMessages() {
        return $this->sentMessages;
    }
}
