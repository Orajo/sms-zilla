<?php

namespace SmsSender;

/**
 * Interface for SMS sender engine
 */
interface SmsSenderInterface {
    
    /**
     * Initialize Sender 
     * @param Adapter\AdapterInterface $adapter
     * @param array $params Adapter configuration
     */
    public function __construct(Adapter\AdapterInterface $adapter, array $params);
    
    /**
     * Add one recipient phone number
     * If $ignoreErrors flag is true then wrong numbers will be ommited and others will be added
     * @param string|array $phoneNo Phone number or list of phone numbers
     * @param bool $ignoreErrors Flag to ignore errors in phone number
     * @return SmsSenderInterface
     */
    public function setRecipient($phoneNo, $ignoreErrors = true);
    
    /**
     * Returns list of recipients (phone numbers)
     * @return array
     */
    public function getRecipients();
    
    /**
     * Sets message content
     * @param string $message
     * @return SmsSenderInterface
     */
    public function setText($message);
    
    /**
     * Returns content of the message
     * @return string
     */
    public function getText();
    
    /**
     * Sets current message object
     * @param \SmsSender\MessageInterface $message
     */
    public function setMessage(MessageInterface $message);

    /**
     * Gets current message object
     * @return \SmsSender\MessageInterface
     */
    public function getMessage();
    
    /**
     * Send message through given adapter (SMS gateway)
     * @return bool
     */
    public function send();
    
    /**
     * Gets adapter object
     * @return Adapter\AdapterInterface
     */
    public function getAdapter();
}
