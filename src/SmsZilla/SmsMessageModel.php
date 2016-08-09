<?php
/**
 * SmsZilla
 * PHP library for sending SMS through various gateways.
 * @link https://github.com/Orajo/sms-zilla Homepage
 * @copyright Copyright (c) 2016 Jarosław Wasilewski <orajo@windowslive.com>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

namespace SmsZilla;

/**
 * Message model
 *
 * @author Jarosław Wasilewski <orajo@windowslive.com>
 */
class SmsMessageModel implements MessageInterface {
    /**
     * List of phone numbers of recipients
     * @var array
     */
    protected $recipients = [];
    
    /**
     * Message content
     * @var string
     */
    protected $text = '';
    
    
    /**
     * Sets message content
     * 
     * @param string $text
     * @return SmsMessageModel
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
     * @return SmsMessageModel
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
