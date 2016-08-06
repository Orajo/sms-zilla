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
 * Interface which can be used to store details of error while sending message
 * to recipient.
 * 
 * @author Jarosław Wasilewski <orajo@windowslive.com>
 */
interface SendingErrorInterface {
    
    public function __construct($recipient, $code, $message = '');
            
    public function getRecipient();
    
    public function getCode();
    
    public function getMessage();
}
