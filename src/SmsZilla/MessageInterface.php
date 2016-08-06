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
 * SMS message container interface
 * 
 * @author Jarosław Wasilewski (orajo@windowslive.com)
 */
interface MessageInterface {
    
    /**
     * Sets message content
     * @param string $text
     * @return MessageInterface
     */
    public function setText($text);

    /**
     * Gets message content
     * @return string
     */
    public function getText();

    /**
     * Adds recipient phone number
     * Recipients number should be: \d{9} or \d{11}
     * @param string $phoneNo
     * @return MessageInterface
     */
    public function addRecipient($phoneNo);

    /**
     * Gets all recipients.
     * @return array
     */
    public function getRecipients();
    
    /**
     * Gets next recipient from list of recipients.
     * @return \Generator
     */
    public function getRecipient();
}
