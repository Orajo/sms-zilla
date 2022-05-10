<?php
/**
 * SmsZilla
 * PHP library for sending SMS through various gateways.
 * @link https://github.com/Orajo/sms-zilla Homepage
 * @copyright Copyright (c) 2016 Jarosław Wasilewski <orajo@windowslive.com>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

namespace SmsZilla;

use SmsZilla\Adapter\AdapterInterface;
use SmsZilla\SmsMessageModel;
use SmsZilla\Validator\ValidatorInterface;

/**
 * Main worker class.
 * 
 * It sends SMS using given adapter class for handling choosen gateway.
 * Message should be configured using this class, becouse it provide additionl
 * validation and error handling. But if you want to, you can set your own 
 * message class implementing {@see MessageInterface}.
 * 
 * @see MessageInterface, Adapter\AdapterInterface
 * @author Jarosław Wasilewski <orajo@windowslive.com>
 */
class SmsSender implements SmsSenderInterface {

    protected $message = null;
    protected $adapter = null;

    /**
     * Phone numbers validator and formatter
     * 
     * @var ValidatorInterface
     */
    protected $validator = null;

    /**
     * Initialize Sender
     * @param AdapterInterface $adapter
     * @param array $params Adapter configuration
     */
    public function __construct(AdapterInterface $adapter, array $params = null) {
        $this->message = new SmsMessageModel();

        $this->adapter = $adapter;
        if (is_array($params) && !empty($params)) {
            $this->adapter->setParams($params);
        }

        // default validator
        try {
            $this->setValidator(new Validator\LibphonenumberValidator());
        }
        catch (\Exception $exp) {
            ; // nothing to do, validation disabled
        }
    }

    /**
     * Sets message content
     * @param string $message
     * @return SmsSender
     * @throws \InvalidArgumentException
     */
    public function setText($message) {
        if (empty($message)) {
            throw new \InvalidArgumentException('Message cannot be empty');
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
     * @return SmsMessageModel
     */
    public function getMessage() {
        if ($this->message instanceof MessageInterface) {
            return $this->message;
        }
        return $this->mesage = new SmsMessageModel();
    }

    /**
     * Add one or many recipients phone numbers
     * If $ignoreErrors flag is true then wrong numbers will be ommited and others will be added.
     * Phone number must be \d{9} or \d{11}
     * @param string|array $phoneNo Phone number or list of phone numbers
     * @param bool $ignoreErrors Flag to ignore errors in phone number
     * @return SmsSender
     */
    public function addRecipient($phoneNo, $ignoreErrors = true) {
        if (!is_array($phoneNo)) {
            $phoneNo = array($phoneNo);
        }

        foreach ($phoneNo as $number) {
            $number = trim($number);
            if ($this->validator instanceof Validator\ValidatorInterface) {
                if ($this->validator->isValid($number)) {
                    $number = $this->validator->format($number);
                    $this->message->addRecipient($number);
                }
                elseif ($ignoreErrors) {
                    continue;
                }
                else {
                    throw new \BadMethodCallException(join(' ', $this->validator->getMessages()));
                }
            }
            else {
                $this->message->addRecipient($number);
            }
        }
        return $this;
    }

    /**
     * Sets recipients phone numbers.
     * Previous recipients will be removed.
     * If $ignoreErrors flag is true then wrong numbers will be ommited and others will be added.
     * Phone number must be \d{9} or \d{11}
     * @param string|array $phoneNo Phone number or list of phone numbers
     * @param bool $ignoreErrors Flag to ignore errors in phone number
     * @return
     */
    public function setRecipients($phoneNo, $ignoreErrors = true) {
        $this->getMessage()->clearRecipients();
        return $this->addRecipient($phoneNo, $ignoreErrors);
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
     * 
     * @uses Adapter\AdapterInterface Using specific adapter class
     * @return bool
     */
    public function send() {
        return $this->getAdapter()->send($this->getMessage());
    }

    /**
     * Gets adapter object
     * @return \SmsZilla\GeatewayInterface
     */
    public function getAdapter() {
        return $this->adapter;
    }

    public function setValidator(ValidatorInterface $validator) {
        $this->validator = $validator;
        return $this;
    }

}
