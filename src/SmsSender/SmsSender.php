<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SmsSender;

use SmsSender\Adapter\GeatewayInterface;
use SmsSender\MessageModel;

/**
 * Description of SmsSender
 *
 * @author Jarek
 */
class SmsSender implements SmsSenderInterface {

    protected $message = null;
    protected $adapter = null;

    /**
     * Initialize Sender
     * @param GeatewayInterface $adapter
     * @param array $params Adapter configuration
     */
    public function __construct(GeatewayInterface $adapter, array $params = null) {
        $this->message = new MessageModel();

        $this->adapter = $adapter;
        if (is_array($params) && !empty($params)) {
            $this->adapter->setParams($params);
        }
    }

    /**
     * Sets message content
     * @param string $message
     * @param string $encoding Encoding name
     * @return SmsSender
     * @throws \InvalidArgumentException
     */
    public function setText($message, $encoding = 'UTF-8') {
        if (empty($message)) {
            throw new \InvalidArgumentException('SMS message cannot be empty');
        }
        if (!empty($encoding)) {
            if (extension_loaded('mbstring')) {
                $message = mb_convert_encoding((string) $message, $encoding);
            }
        }
        $this->message->setText($message);
        return $this;
    }

    /**
     * Gets message content
     * @return string
     */
    public function getText() {
        return $this->getMessage()->getText();
    }

    /**
     * Sets current message object
     * @param MessageInterface $message
     * @return SmsSender
     */
    public function setMessage(MessageInterface $message) {
        $this->message = $message;
        return $this;
    }

    /**
     * Gets current message object
     * @return MessageModel
     */
    public function getMessage() {
        if ($this->message instanceof MessageInterface) {
            return $this->message;
        }
        return $this->mesage = new MessageModel();
    }

    /**
     * Add one recipient phone number
     * If $ignoreErrors flag is true then wrong numbers will be ommited and others will be added.
     * Phone number must be \d{9} or \d{11}
     * @param string|array $phoneNo Phone number or list of phone numbers
     * @param bool $ignoreErrors Flag to ignore errors in phone number
     * @return SmsSender
     */
    public function setRecipient($phoneNo, $ignoreErrors = true) {
        if (!is_array($phoneNo)) {
            $phoneNo = array($phoneNo);
        }

        foreach ($phoneNo as $number) {
            $number = trim($number);
            $number = preg_replace('/\s\-\+/', '', $number);
            if (preg_match('/^(\d{9}|\d{11})$/', $number)) {
                $number = strlen($number) == 9 ? '48' . $number : $number;
                $this->message->addRecipient($number);
            }
            elseif ($ignoreErrors) {
                continue;
            }
            else {
                throw new \BadMethodCallException('Phone number has incorrect format. It should be 9 or 11 digits');
            }
        }
        return $this;
    }

    /**
     * Gets recipients list
     * @return array
     */
    public function getRecipients() {
        return $this->getMessage()->getRecipients();
    }

    /**
     * Send message through given adapter (SMS gateway)
     * @return bool
     */
    public function send() {
        return $this->getAdapter()->send($this->getMessage());
    }

    /**
     * Gets adapter object
     * @return \SmsSender\GeatewayInterface
     */
    public function getAdapter() {
        return $this->adapter;
    }

}
