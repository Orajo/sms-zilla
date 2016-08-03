<?php

/**
 * This file is part of SmsSender package
 * @author Jarosław Wasilewski (orajo@windowslive.com)
 */
namespace SmsSender;

/**
 * SMS message container interface
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
