<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SmsSender;

/**
 * Description of MessageModel
 *
 * @author Jarek
 */
class MessageModel implements MessageInterface {
    
    protected $recipients = [];

    protected $sender;
    
    protected $text = '';
    
    
    /**
     * Sets message content
     * 
     * @param string $text
     * @return MessageModel
     * @throws \BadMethodCallException
     */
    public function setText($text) {
        if (empty($text)) {
            throw new \InvalidArgumentException('SMS message cannot be empty');
        }
        $this->text = $text;
        return $this;
    }

    /**
     * Gets message content
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * Adds recipient phone number
     * Recipients number should be: \d{9} or \d{11}
     * @param string $phoneNo
     * @return MessageModel
     */
    public function addRecipient($phoneNo) {
        
        if (!empty($phoneNo)) {
            $this->recipients[] = (string)$phoneNo;
        }
        else { 
            throw new \InvalidArgumentException('Phone number cannot be empty.');
        }
        return $this;
    }

    /**
     * Gets all recipients.
     * @return array
     */
    public function getRecipients(){
        return $this->recipients;
    }
    
    /**
     * Gets next recipient from list of recipients.
     * Implements generator through yield
     * @return \Generator
     */
    public function getRecipient() {
        foreach ($this->recipients as $recipient) {
            yield $recipient;
        }
    }
}
