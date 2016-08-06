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
 * Model of error message, which can hold details of error of sending message
 * to selcted recipient.
 *
 * @see Adapter\AbstractAdapter::getErrors
 * @author Jarosław Wasilewski <orajo@windowslive.com>
 */
class SendingError implements \SmsZilla\SendingErrorInterface {

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
